# Install PHP Composer packages
FROM composer:2.7 AS composer

WORKDIR /usr/app

COPY . /usr/app/

RUN composer install --no-dev

# Install Node packages
FROM node:20 AS node

WORKDIR /usr/app

COPY . /usr/app/

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        python3 \
    && npm install \
    && npm run prod

# Set up Apache server
FROM php:8.1-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

COPY . /var/www/html/

COPY --from=composer /usr/app/ /var/www/html/

COPY --from=node /usr/app/public/js/ /var/www/html/public/js/
COPY --from=node /usr/app/public/css/ /var/www/html/public/css/

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && mv .env.template .env \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    # Set permissions
    && chown -R www-data:www-data /var/www/html/storage/framework \
    && chown -R www-data:www-data /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html/storage/framework -type d -exec chmod 775 {} \; \
    && find /var/www/html/storage/logs -type d -exec chmod 775 {} \; \
    && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; \
    # Install zip PHP extension
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip pdo_mysql mysqli \
    # Enable Apache modules
    && a2enmod rewrite
