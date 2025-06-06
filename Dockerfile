ARG MEDIAWIKI_VERSION=1.39.12
ARG ALPINE_VERSION=3.20
ARG PHP_VERSION=83

# Download mediawiki
FROM alpine:$ALPINE_VERSION as builder
ARG MEDIAWIKI_VERSION
ARG PHP_VERSION

RUN mkdir -p /tmp/composer /tmp/mediawiki
COPY --from=ghcr.io/shinycolorswiki/mediawiki-extension-downloader:latest /app /tmp/mediawiki-extension-downloader
COPY config/extension-list.json /tmp/mediawiki-extension-downloader.json

# Install PHP Redis and some other extensions
RUN apk add --update --no-cache \
    curl tar gzip \
    # PHPs
    php${PHP_VERSION} php${PHP_VERSION}-fpm \
    # Mediawiki requirements
    php${PHP_VERSION}-session php${PHP_VERSION}-openssl php${PHP_VERSION}-json php${PHP_VERSION}-mbstring php${PHP_VERSION}-fileinfo \
    php${PHP_VERSION}-intl php${PHP_VERSION}-calendar php${PHP_VERSION}-xml \
    # Mediawiki configuration requirements.
    php${PHP_VERSION}-curl php${PHP_VERSION}-mysqli php${PHP_VERSION}-mysqlnd php${PHP_VERSION}-gd php${PHP_VERSION}-dom php${PHP_VERSION}-ctype \
    php${PHP_VERSION}-iconv php${PHP_VERSION}-zlib php${PHP_VERSION}-xmlreader \
    # Mediawiki caching and extensions requirements
    php${PHP_VERSION}-simplexml php${PHP_VERSION}-tokenizer php${PHP_VERSION}-xmlwriter php${PHP_VERSION}-opcache php${PHP_VERSION}-phar \
    php${PHP_VERSION}-pecl-apcu php${PHP_VERSION}-pecl-redis php${PHP_VERSION}-pcntl php${PHP_VERSION}-posix \
    # Composer
    composer

ARG BUILD_VER=0

RUN MEDIAWIKI_MAJOR_VERSION="$(echo ${MEDIAWIKI_VERSION} | cut -d. -f-2)" MWREL="REL$(echo ${MEDIAWIKI_VERSION} | cut -d. -f-2 | sed 's/\./_/g')" \
    && curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-core-${MEDIAWIKI_VERSION}.tar.gz" -o mediawiki.tar.gz \
    && tar -xzf mediawiki.tar.gz --strip-components=1 --directory /tmp/mediawiki/ \
    && rm mediawiki.tar.gz

RUN /tmp/mediawiki-extension-downloader --config /tmp/mediawiki-extension-downloader.json --target /tmp/mediawiki --force-rm-target=true

COPY config/wiki/composer.local.json /tmp/mediawiki/
WORKDIR /tmp/mediawiki

# Composer needs local user or set the flag for plugins. (that includes installer)
RUN COMPOSER_HOME=/tmp/composer COMPOSER_ALLOW_SUPERUSER=1 /usr/bin/php$PHP_VERSION /usr/bin/composer.phar update --no-dev

# NO I WON'T USE PHP IMAGE SINCE IT'S TOO BIG
FROM alpine:$ALPINE_VERSION
ARG PHP_VERSION

# LuaSandbox package is added on community branch (as of v3.19) ).
RUN apk add --update --no-cache \
    # Basic utils
    curl imagemagick rsvg-convert diffutils ffmpeg sudo lua tar bzip2 zstd bash mariadb-client \
    # Web server
    nginx \ 
    # See https://github.com/krallin/tini.
    tini \
    # PHPs
    php${PHP_VERSION} php${PHP_VERSION}-fpm \
    # Mediawiki requirements
    php${PHP_VERSION}-session php${PHP_VERSION}-openssl php${PHP_VERSION}-json php${PHP_VERSION}-mbstring php${PHP_VERSION}-fileinfo \
    php${PHP_VERSION}-intl php${PHP_VERSION}-calendar php${PHP_VERSION}-xml \
    # Mediawiki configuration requirements.
    php${PHP_VERSION}-curl php${PHP_VERSION}-mysqli php${PHP_VERSION}-mysqlnd php${PHP_VERSION}-gd php${PHP_VERSION}-dom php${PHP_VERSION}-ctype \
    php${PHP_VERSION}-iconv php${PHP_VERSION}-zlib php${PHP_VERSION}-xmlreader php${PHP_VERSION}-pecl-luasandbox \
    # Mediawiki caching and extensions requirements
    php${PHP_VERSION}-simplexml php${PHP_VERSION}-tokenizer php${PHP_VERSION}-xmlwriter php${PHP_VERSION}-opcache php${PHP_VERSION}-phar \
    php${PHP_VERSION}-pecl-apcu php${PHP_VERSION}-pecl-redis php${PHP_VERSION}-pcntl php${PHP_VERSION}-posix

# Make folder and copy mediawiki into here.
RUN mkdir /srv/wiki && chown nginx:www-data /srv/wiki && \
    sudo -u nginx -g www-data mkdir -p /srv/wiki/w/ /srv/wiki/sitemap
COPY --from=builder --chown=nginx:www-data /tmp/mediawiki /srv/wiki/w/

# Set Permissions here.
# Widgets need permission to write compiled template.
RUN chmod o+w /srv/wiki/w/extensions/Widgets/compiled_templates

# Copy misc resources like robots.txt and favicon.ico
COPY --chown=nginx:www-data resources /srv/wiki/

# Copy settings to `/setting/wiki`. but still need `secret.php`
COPY --chown=nginx:www-data config/wiki/LocalSettings.php config/wiki/ExtensionSettings.php /setting/wiki/
VOLUME /setting

# Install S3 uploader and temporal environment.
COPY --from=ghcr.io/shinycolorswiki/s3-uploader /app /usr/local/bin/s3-uploader
COPY dev/aws.example.env /etc/aws.env

# Install Run script and crons
COPY run \
     cron/generate-backup \
     cron/generate-dumps \
     cron/generate-sitemap \
     cron/run-jobs \
     cron/run-transcode-jobs \
     cron/update-sfs \
     # Well just reduce stage...
     cron/crontab_config \
     /usr/local/bin/
RUN crontab /usr/local/bin/crontab_config && rm /usr/local/bin/crontab_config \
    && bash -c 'chmod +x /usr/local/bin/{run,generate-backup,generate-dumps,generate-sitemap,run-jobs,update-sfs}'

ENV XDG_CONFIG_HOME /config
ENV XDG_DATA_HOME /data

WORKDIR /srv/wiki

# tini, https://github.com/krallin/tini
ENTRYPOINT ["/sbin/tini", "--"]

EXPOSE 80
EXPOSE 443
EXPOSE 9000

# Thanks to https://stackoverflow.com/a/64041910
ENV HEALTHCHECK_URL "http://http:8080/w/api.php?action=query&meta=siteinfo&siprop=statistics&format=json"
HEALTHCHECK --interval=5m --timeout=2m --start-period=60s \
    CMD curl \
        -sf \
        -H 'Cache-Control: no-cache, no-store' \
        --connect-timeout 30 \
        --max-time 30 \
        --retry 3 \
        --retry-delay 20 \
        --retry-max-time 90 \
        "${HEALTHCHECK_URL}" || exit 1

CMD ["bash", "/usr/local/bin/run"]
