#!/bin/bash

set -euo pipefail

if [[ ! -d /data ]]; then
    echo "/data does not exist" >&2
    exit 1
fi

uid="$(stat -c %u /data)"
gid="$(stat -c %g /data)"

usermod -u "$uid" www-data
groupmod -g "$gid" www-data

chown -R "$uid:$gid" /var/www

socat TCP-LISTEN:26740,reuseaddr,fork TCP:localhost:80 &

exec docker-php-entrypoint apache2-foreground
