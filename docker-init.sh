#!/bin/bash

STORAGE_DIR="/var/www/html/storage"
CONFIG_DIR="/etc/inventory-booking-system/config"
KEY_DIR="/etc/ssl/private"
CA_DIR="/etc/ssl/certs"

# Check if storage directory is empty
if [ -z "$(ls -A $STORAGE_DIR)" ]; then
    echo "Storage directory is empty. Creating file structure."
    mkdir -p $STORAGE_DIR/framework/cache
    mkdir -p $STORAGE_DIR/framework/sessions
    mkdir -p $STORAGE_DIR/framework/views
    mkdir -p $STORAGE_DIR/logs
    chown -R www-data:www-data $STORAGE_DIR
    find $STORAGE_DIR -type d -exec chmod 775 {} \;
fi

# Check if config directory is empty
if [ -z "$(ls -A $CONFIG_DIR)" ]; then
    echo "Config directory is empty. Copying .env file."
    cp .env.template $CONFIG_DIR/.env
    # Redirect Laravel logs to stdout
    echo -e "\nLOG_CHANNEL=docker" >> .env
    chown www-data:www-data $CONFIG_DIR/.env
fi

service cron start

# Generate CA Key if it doesn't exist
if [ ! -f "$KEY_DIR/ca.key" ]; then
    openssl genrsa -out $KEY_DIR/ca.key 4096
fi

# Generate Certificate if it doesn't exist
if [ ! -f "$CA_DIR/ca.crt" ]; then
    openssl req -x509 -new -nodes -key $KEY_DIR/ca.key -sha256 -days 3650 -out $CA_DIR/ca.crt -subj "/C=US/ST=State/L=City/O=Company/CN=example.com CA"
fi

# Configure Apache to use the generated SSL Certificate
sed -i "s|SSLCertificateFile.*|SSLCertificateFile $CA_DIR/ca.crt|" /etc/apache2/sites-available/default-ssl.conf
sed -i "s|SSLCertificateKeyFile.*|SSLCertificateKeyFile $KEY_DIR/ca.key|" /etc/apache2/sites-available/default-ssl.conf

# Generate a new key and certificate request
openssl genrsa -out $CA_DIR/server.key 2048
openssl req -new -key $CA_DIR/server.key -out $CA_DIR/server.csr -subj "/C=US/ST=State/L=City/O=Company/CN=example.com"

# Sign the request with the CA
openssl x509 -req -in $CA_DIR/server.csr -CA $CA_DIR/ca.crt -CAkey $KEY_DIR/ca.key -CAcreateserial -out $CA_DIR/server.crt -days 365 -sha256

exec "$@"
