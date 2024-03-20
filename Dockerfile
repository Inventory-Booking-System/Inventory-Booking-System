FROM composer:2.7 AS composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

FROM node:21-alpine AS node

COPY package.json package-lock.json ./
RUN npm install \
    && npm run prod

FROM php:8.1-apache

COPY --from=composer /app/vendor/ /var/www/html/vendor/
COPY --from=node ./node_modules/ /var/www/html/node_modules/

COPY . /var/www/html/

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
