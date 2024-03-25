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
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; \
    # Redirect Laravel logs to stdout
    && touch /var/www/html/storage/logs/laravel.log \
    && ln -sf /dev/stdout /var/www/html/storage/logs/laravel.log \
    # Install dependencies
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        openssl \
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
    && mv generate-cert.sh /usr/local/bin/generate-cert.sh \
    && chmod +x /usr/local/bin/generate-cert.sh \
    # Configure Apache to use the generated SSL Certificate
    && sed -i 's|SSLCertificateFile.*|SSLCertificateFile /etc/ssl/certs/ca.crt|' /etc/apache2/sites-available/default-ssl.conf \
    && sed -i 's|SSLCertificateKeyFile.*|SSLCertificateKeyFile /etc/ssl/private/ca.key|' /etc/apache2/sites-available/default-ssl.conf

ENTRYPOINT ["generate-cert.sh"]
CMD ["apache2-foreground"]

# Expose port 443 for SSL
EXPOSE 443