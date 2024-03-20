FROM composer:2.7 AS composer

WORKDIR /usr/app

COPY composer.json composer.lock /usr/app/

RUN composer install --no-dev --no-scripts --no-autoloader


FROM node:21-alpine AS node

WORKDIR /usr/app

COPY package.json package-lock.json /usr/app/

RUN npm install \
    && npm run prod


FROM php:8.1-apache

COPY . /var/www/html/

COPY --from=composer /usr/app/vendor/ /var/www/html/vendor/
COPY --from=node /usr/app/node_modules/ /var/www/html/node_modules/

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
