#
# Install MediaWiki Extension
#
# Python Buster package
FROM python:3-buster AS base

ARG MEDIAWIKI_MAJOR_VERSION=1.35
ARG MEDIAWIKI_BRANCH=REL1_35
ARG MEDIAWIKI_VERSION=1.35.1


RUN apt-get update && apt-get install -y \
        # requirements of composer
        php-cli \
        php-mbstring \
        # requirement of prestissimo
        php-curl \
        # requirements of aws-sdk-php
        php-simplexml \
        sudo

# Get Composer
# Note: Some composer plugin doesn't support Composer 2.x for now.
# https://phabricator.wikimedia.org/T248908
COPY --from=composer:1 /usr/bin/composer /usr/local/bin/composer

# Install prestissimo (composer 1.x parallel plugin)
RUN composer global require hirak/prestissimo

# Create a cache directory for composer and extension. Also mediawiki folder
RUN sudo -u www-data mkdir -p /tmp/composer /tmp/extension_temp /tmp/mediawiki/extensions

# Download/Setup Mediawiki extensions
COPY extensions/* /tmp/
RUN sudo -u www-data python /tmp/install.py "${MEDIAWIKI_BRANCH}"

# MediaWiki setup
COPY --chown=www-data config/composer.local.json /tmp/mediawiki/
RUN curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-core-${MEDIAWIKI_VERSION}.tar.gz" -o mediawiki.tar.gz &&\
    sudo -u www-data tar -xzf mediawiki.tar.gz --strip-components=1 --directory /tmp/mediawiki/ &&\
    rm mediawiki.tar.gz
RUN sudo -u www-data COMPOSER_HOME=/tmp/composer composer update --no-dev --working-dir '/tmp/mediawiki'


