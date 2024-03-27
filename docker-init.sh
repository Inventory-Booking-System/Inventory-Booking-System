#!/bin/bash

STORAGE_DIR="/var/www/html/storage"
CONFIG_DIR="/etc/inventory-booking-system/config"

# Check if storage directory is empty
if [ -z "$(ls -A $STORAGE_DIR)" ]; then
   echo "Storage directory is empty. Creating file structure."
   mkdir -p $STORAGE_DIR/framework/cache
   mkdir -p $STORAGE_DIR/framework/sessions
   mkdir -p $STORAGE_DIR/framework/views
fi

# Check if config directory is empty
if [ -z "$(ls -A $CONFIG_DIR)" ]; then
   echo "Config directory is empty. Copying .env file."
   cp .env.template $CONFIG_DIR/.env
fi

# Generate a new key and certificate request
openssl genrsa -out /etc/ssl/private/server.key 2048
openssl req -new -key /etc/ssl/private/server.key -out /etc/ssl/certs/server.csr -subj "/C=US/ST=State/L=City/O=Company/CN=example.com"

# Sign the request with the CA
openssl x509 -req -in /etc/ssl/certs/server.csr -CA /etc/ssl/certs/ca.crt -CAkey /etc/ssl/private/ca.key -CAcreateserial -out /etc/ssl/certs/server.crt -days 365 -sha256

# Update Apache configuration if necessary
# Note: Ensure your Apache configuration points to /etc/ssl/certs/server.crt and /etc/ssl/private/server.key

exec "$@"
