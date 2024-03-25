#!/bin/bash

# Generate a new key and certificate request
openssl genrsa -out /etc/ssl/private/server.key 2048
openssl req -new -key /etc/ssl/private/server.key -out /etc/ssl/certs/server.csr -subj "/C=US/ST=State/L=City/O=Company/CN=example.com"

# Sign the request with the CA
openssl x509 -req -in /etc/ssl/certs/server.csr -CA /etc/ssl/certs/ca.crt -CAkey /etc/ssl/private/ca.key -CAcreateserial -out /etc/ssl/certs/server.crt -days 365 -sha256

# Update Apache configuration if necessary
# Note: Ensure your Apache configuration points to /etc/ssl/certs/server.crt and /etc/ssl/private/server.key

exec "$@"
