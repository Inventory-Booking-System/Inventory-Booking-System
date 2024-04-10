# Install PHP Composer packages
FROM composer:2.7 AS composer

WORKDIR /usr/app

COPY . /usr/app/

RUN composer install --optimize-autoloader --no-dev

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

COPY --from=node /usr/app/public/ /var/www/html/public/

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    # Use the production PHP configuration
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    # Create and link .env file
    && mkdir -p /etc/inventory-booking-system/config \
    && touch /etc/inventory-booking-system/config/.env \
    && ln -sf /etc/inventory-booking-system/config/.env /var/www/html/.env \
    && chown --no-dereference www-data:www-data /var/www/html/.env \
    && chmod 664 /var/www/html/.env \
    # Set permissions
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; \
    # Install dependencies
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        openssl \
        supervisor \
        cron \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    # Install PHP extensions
    && docker-php-ext-install zip pdo_mysql mysqli \
    # Enable Apache modules
    && a2enmod rewrite ssl \
    && a2ensite default-ssl \
    # Generate CA Key and Certificate
    && openssl genrsa -out /etc/ssl/private/ca.key 4096 \
    && openssl req -x509 -new -nodes -key /etc/ssl/private/ca.key -sha256 -days 3650 -out /etc/ssl/certs/ca.crt -subj "/C=US/ST=State/L=City/O=Company/CN=example.com CA" \
    && mv docker-init.sh /usr/local/bin/docker-init.sh \
    && chmod +x /usr/local/bin/docker-init.sh \
    # Configure Apache to use the generated SSL Certificate
    && sed -i 's|SSLCertificateFile.*|SSLCertificateFile /etc/ssl/certs/ca.crt|' /etc/apache2/sites-available/default-ssl.conf \
    && sed -i 's|SSLCertificateKeyFile.*|SSLCertificateKeyFile /etc/ssl/private/ca.key|' /etc/apache2/sites-available/default-ssl.conf \
    # Create Laravel Scheduler Cron Job
    && echo "* * * * * cd /var/www/html && php artisan schedule:run" > /etc/cron.d/laravel-scheduler \
    # Configure Supervisor
    && mkdir -p /etc/supervisor/conf.d \
    && mv supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

ENTRYPOINT ["docker-init.sh"]
CMD ["supervisord -c /etc/supervisor/conf.d/supervisord.conf"]

EXPOSE 443