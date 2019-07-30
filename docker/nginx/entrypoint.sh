#! /bin/sh

set -e

if [ "${APP_DEBUG}" = 1 ]; then
    set -x
fi

envsubst '${SERVICE_UPSTREAM}' < /etc/nginx/conf.d/default.template > /etc/nginx/conf.d/default.conf

exec "$@"
