FROM composer:2.7 AS composer

WORKDIR /usr/app

COPY . /usr/app/

RUN composer install --no-dev


FROM node:20 AS node

WORKDIR /usr/app

COPY . /usr/app/

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        python3 \
    && npm install \
    && npm run prod


FROM php:8.1-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

COPY . /var/www/html/

COPY --from=composer /usr/app/vendor/ /var/www/html/vendor/
COPY --from=node /usr/app/public/js/ /var/www/html/public/js/

RUN mv .env.template .env \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
