#! /bin/sh

set -e

if [ "${APP_DEBUG}" = 1 ]; then
    set -x
fi

if [ "${COMPOSER_INSTALL}" = 1 ]; then
    composer install \
        --no-ansi \
        --no-interaction \
        --no-scripts \
        --no-suggest \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader
fi

docker-php-entrypoint "$@"
