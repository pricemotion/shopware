FROM php:7.4.23-apache

ADD https://github.com/mlocati/docker-php-extension-installer/releases/download/1.2.60/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y \
        socat \
        jq \
        npm

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        curl \
        dom \
        fileinfo \
        gd \
        iconv \
        intl \
        json \
        libxml \
        mbstring \
        openssl \
        pcre \
        pdo \
        pdo_mysql \
        phar \
        simplexml \
        sodium \
        xml \
        zip \
        zlib

RUN a2enmod rewrite

COPY entrypoint.sh /

RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh
