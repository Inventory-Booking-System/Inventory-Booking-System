FROM php:8.1-apache

ARG CA_BUNDLE
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

COPY ${CA_BUNDLE} /usr/local/share/ca-certificates/ca-bundle.crt
RUN update-ca-certificates
ENV NODE_EXTRA_CA_CERTS=/usr/local/share/ca-certificates/ca-bundle.crt

COPY ../docker-init.sh ../artisan /var/www/html/
COPY ../supervisor/supervisord.conf /etc/supervisor/conf.d/

# Install packages
RUN apt-get update \
    && apt-get install -y \
        curl \
        gnupg2 \
        ca-certificates \
        lsb-release \
        python3 \
        libzip-dev \
        openssl \
        supervisor \
        cron \
        git \
        dos2unix \
    && curl -fsSL https://deb.nodesource.com/setup_20.x -o /tmp/nodesource_setup.sh \
        2>&1 | tee /tmp/curl-download.log \
    && bash /tmp/nodesource_setup.sh \
        2>&1 | tee /tmp/nodesource-setup.log \
    && apt-get install -y \
        nodejs 2>&1 | tee -a /tmp/node-install.log \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.7 /usr/bin/composer /usr/local/bin/composer

# Install PHP extensions
RUN docker-php-ext-install zip pdo_mysql mysqli

# Enable Apache modules
RUN a2enmod rewrite ssl \
    && a2ensite default-ssl

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
    && mkdir -p /var/www/html/storage/framework \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; \
    # Create and link CA Key and Certificate
    && touch /etc/ssl/private/ca.key \
    && touch /etc/ssl/certs/ca.crt \
    && ln -sf /etc/inventory-booking-system/config/ca.key /etc/ssl/private/ca.key \
    && ln -sf /etc/inventory-booking-system/config/ca.crt /etc/ssl/certs/ca.crt \
    # Create Laravel Scheduler Cron Job
    && echo "* * * * * cd /var/www/html && php artisan schedule:run" > /etc/cron.d/laravel-scheduler

# Configure Docker Init script. We need to change CRLF to LF to execute
RUN dos2unix docker-init.sh \
    && mv docker-init.sh /usr/local/bin/docker-init.sh \
    && chmod +x /usr/local/bin/docker-init.sh

ENTRYPOINT ["docker-init.sh"]
CMD ["/usr/bin/supervisord"]

EXPOSE 80