# BASE IMAGE

FROM php:7.3-fpm-alpine AS base

ENTRYPOINT ["entrypoint"]

CMD ["php-fpm"]

ENV PHP_EXT_DIR=/usr/src/php/ext

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="10000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="192" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10"

WORKDIR /app

RUN apk add --no-cache --update \
    libzip \
    zlib \
    gmp \
    git \
    && apk add --no-cache --update --virtual .build-deps $PHPIZE_DEPS \
    libzip-dev \
    zlib-dev \
    gmp-dev \
    && docker-php-ext-install \
    pdo_mysql \
    opcache \
    bcmath \
    zip \
    gmp \
    && apk del .build-deps \
    && rm -rf /tmp/*

COPY --chown=www-data:www-data docker/app/confs/opcache.ini $PHP_INI_DIR/conf.d/opcache.ini

COPY --chown=www-data:www-data docker/app/entrypoint.sh /usr/bin/entrypoint

# VENDOR BUILD

FROM composer:1.8 AS vendor

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /composer

COPY src/database database

COPY src/composer.* ./

RUN composer global require hirak/prestissimo \
    && composer install \
    --ignore-platform-reqs \
    --no-dev \
    --no-ansi \
    --no-autoloader \
    --no-interaction \
    --no-scripts \
    --no-suggest \
    --no-progress \
    --prefer-dist

# DEV BUILD

FROM base AS dev

ARG XDEBUG_VERSION=2.7.2

ENV XDEBUG_IDE_KEY="" \
    XDEBUG_REMOTE_PORT=9000 \
    XDEBUG_REMOTE_HOST=127.0.0.1 \
    COMPOSER_INSTALL=1 \
    COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
    && mkdir -p /var/log/xdebug \
    && touch /var/log/xdebug/xdebug.log \
    && chown www-data:www-data /var/log/xdebug/xdebug.log \
    && apk del .phpize-deps && rm -rf /var/cache/apk/* \
    && mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY --chown=www-data:www-data ./docker/app/confs/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini
COPY --chown=www-data:www-data --from=vendor /usr/bin/composer /usr/bin/composer
COPY --chown=www-data:www-data --from=vendor /composer/vendor vendor
COPY --chown=www-data:www-data src/tests tests
COPY --chown=www-data:www-data src/composer.* ./
COPY --chown=www-data:www-data src/phpunit.xml phpunit.xml

RUN composer global require hirak/prestissimo \
    && composer install \
    --no-ansi \
    --no-autoloader \
    --no-interaction \
    --no-scripts \
    --no-suggest \
    --no-progress \
    --prefer-dist

COPY --chown=www-data:www-data src .

RUN composer dump-autoload -o -a \
    && chown www-data:www-data /app

USER www-data

# PROD BUILD

FROM base AS prod

ENV APP_ENV=production \
    COMPOSER_ALLOW_SUPERUSER=1

COPY --chown=www-data:www-data --from=vendor /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data --from=vendor /composer/vendor vendor

COPY --chown=www-data:www-data src .

RUN composer dump-autoload -o -a --no-dev \
    && chown -R www-data:www-data /app \
    && rm /usr/bin/composer \
    && mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

USER www-data
