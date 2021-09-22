#!/bin/bash

set -euo pipefail

if [[ ! -d /data ]]; then
    echo "/data does not exist" >&2
    exit 1
fi

usermod -u "$(stat -c %u /data)" www-data
groupmod -g "$(stat -c %g /data)" www-data

socat TCP-LISTEN:26740,reuseaddr,fork TCP:localhost:80 &

exec docker-php-entrypoint apache2-foreground
