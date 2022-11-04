ARG MEDIAWIKI_VERSION=1.39.0-rc.1

# PHP Dependency
ARG PHPREDIS_VERSION=5.3.7

# Download mediawiki
FROM alpine:3.16 as builder
ARG MEDIAWIKI_VERSION
ARG PHPREDIS_VERSION

RUN mkdir -p /tmp/composer /tmp/mediawiki
COPY --from=ghcr.io/shinycolorswiki/mediawiki-extension-downloader:latest /app /tmp/mediawiki-extension-downloader
COPY config/extension-list.json /tmp/mediawiki-extension-downloader.json

# Install PHP Redis and some other extensions
RUN apk add --update --no-cache \
    curl tar gzip \
    # PHPs
    php8 php8-fpm \
    # Mediawiki requirements
    php8-session php8-openssl php8-json php8-mbstring php8-fileinfo php8-intl php8-calendar php8-xml \
    # Mediawiki configuration requirements.
    php8-curl php8-mysqli php8-mysqlnd php8-gd php8-dom php8-ctype php8-iconv php8-zlib php8-xmlreader \
    # Mediawiki caching and extensions requirements
    php8-simplexml php8-tokenizer php8-xmlwriter php8-opcache php8-phar php8-pecl-apcu php8-pecl-redis \
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
# v3.16 doesn't have php7. Wait for mediawiki supports php 8.
FROM alpine:3.16

RUN apk add --update --no-cache \
    # Basic utils
    curl imagemagick diffutils ffmpeg sudo lua tar bzip2 zstd bash mariadb-client \
    # Web server
    caddy \ 
    # See https://github.com/krallin/tini.
    tini \
    # PHPs
    php8 php8-fpm \
    # Mediawiki requirements
    php8-session php8-openssl php8-json php8-mbstring php8-fileinfo php8-intl php8-calendar php8-xml \
    # Mediawiki configuration requirements.
    php8-curl php8-mysqli php8-mysqlnd php8-gd php8-dom php8-ctype php8-iconv php8-zlib php8-xmlreader \
    # Mediawiki caching and extensions requirements
    php8-simplexml php8-tokenizer php8-xmlwriter php8-opcache php8-phar php8-pecl-apcu php8-pecl-redis 

# Make folder and copy mediawiki into here.
RUN mkdir /srv/wiki && chown caddy:www-data /srv/wiki && \
    sudo -u caddy -g www-data mkdir -p /srv/wiki/w/ /srv/wiki/sitemap
COPY --from=builder --chown=caddy:www-data /tmp/mediawiki /srv/wiki/w/

# Set Permissions here.
# Widgets need permission to write compiled template.
RUN chmod o+w /srv/wiki/w/extensions/Widgets/compiled_templates

# Copy misc resources like robots.txt and favicon.ico
COPY --chown=caddy:www-data resources /srv/wiki/

# Copy settings to `/setting/wiki`. but still need `secret.php`
COPY --chown=caddy:www-data config/wiki/LocalSettings.php config/wiki/ExtensionSettings.php /setting/wiki/
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
     # Well just reduce stage...
     cron/crontab_config \
     /usr/local/bin/
RUN crontab /usr/local/bin/crontab_config && rm /usr/local/bin/crontab_config \
    && bash -c 'chmod +x /usr/local/bin/{run,generate-backup,generate-dumps,generate-sitemap,run-jobs}' \
    # Caddy directories
    mkdir -p \
      /config/caddy \
      /data/caddy

ENV XDG_CONFIG_HOME /config
ENV XDG_DATA_HOME /data

WORKDIR /srv/wiki

# tini, https://github.com/krallin/tini
ENTRYPOINT ["/sbin/tini", "--"]

EXPOSE 80
EXPOSE 443
EXPOSE 9000

CMD ["bash", "/usr/local/bin/run"]