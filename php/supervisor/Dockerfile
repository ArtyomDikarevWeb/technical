FROM php:8.2-fpm-alpine

RUN apk add --no-cache curl git build-base zlib-dev oniguruma-dev autoconf bash libpq-dev python3 py3-pip supervisor

RUN docker-php-ext-install pdo pdo_pgsql

RUN pecl install redis && docker-php-ext-enable redis

COPY ./php/supervisor/supervisord.conf /etc/supervisord.conf
COPY ./php/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

ARG PUID=1000
ARG PGID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

WORKDIR /etc/supervisor/conf.d/

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]
