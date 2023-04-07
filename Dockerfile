ARG MEDIAWIKI_VERSION=1.39.3

# Download mediawiki
FROM alpine:3.17 as builder
ARG MEDIAWIKI_VERSION

RUN mkdir -p /tmp/composer /tmp/mediawiki
COPY --from=ghcr.io/shinycolorswiki/mediawiki-extension-downloader:latest /app /tmp/mediawiki-extension-downloader
COPY config/extension-list.json /tmp/mediawiki-extension-downloader.json

# Install PHP Redis and some other extensions
RUN apk add --update --no-cache \
    curl tar gzip \
    # PHPs
    php81 php81-fpm \
    # Mediawiki requirements
    php81-session php81-openssl php81-json php81-mbstring php81-fileinfo php81-intl php81-calendar php81-xml \
    # Mediawiki configuration requirements.
    php81-curl php81-mysqli php81-mysqlnd php81-gd php81-dom php81-ctype php81-iconv php81-zlib php81-xmlreader \
    # Mediawiki caching and extensions requirements
    php81-simplexml php81-tokenizer php81-xmlwriter php81-opcache php81-phar php81-pecl-apcu php81-pecl-redis \
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
RUN COMPOSER_HOME=/tmp/composer composer update --no-dev

# NO I WON'T USE PHP IMAGE SINCE IT'S TOO BIG
FROM alpine:3.17

# LuaSandbox package is added on testing branch. we need to add for luasandbox.
RUN echo "@testing https://dl-cdn.alpinelinux.org/alpine/edge/testing" >> /etc/apk/repositories && \
    apk add --update --no-cache \
    # Basic utils
    curl imagemagick rsvg-convert diffutils ffmpeg sudo lua tar bzip2 zstd bash mariadb-client \
    # Web server
    nginx \ 
    # See https://github.com/krallin/tini.
    tini \
    # PHPs
    php81 php81-fpm \
    # Mediawiki requirements
    php81-session php81-openssl php81-json php81-mbstring php81-fileinfo php81-intl php81-calendar php81-xml \
    # Mediawiki configuration requirements.
    php81-curl php81-mysqli php81-mysqlnd php81-gd php81-dom php81-ctype php81-iconv php81-zlib php81-xmlreader \
    # Mediawiki caching and extensions requirements
    php81-simplexml php81-tokenizer php81-xmlwriter php81-opcache php81-phar php81-pecl-apcu php81-pecl-redis php81-pecl-luasandbox@testing

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

ENV HEALTHCHECK_URL http://http:8080
HEALTHCHECK --interval=5m --timeout=2m --start-period=15s \
    CMD curl -f -L --retry 6 --max-time 5 --retry-delay 5 --retry-max-time 60 "${HEALTHCHECK_URL}" || bash -c 'kill -s 15 -1 && (sleep 10; kill -s 9 -1)'

CMD ["bash", "/usr/local/bin/run"]
