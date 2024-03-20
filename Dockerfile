FROM php:8.1-apache
COPY . /var/www/html/

RUN apt update \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt install -y --no-install-recommends \
        nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && cd /var/www/html/ \
    && composer install \
    && npm install \
    && npm run prod

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
