FROM php:8.1-apache
RUN apk add zip &&\
    curl https://github.com/Inventory-Booking-System/Inventory-Booking-System/releases/latest/download/Inventory-Booking-System.zip --output Inventory-Booking-System.zip && \
    unzip Inventory-Booking-System.zip -d /var/www/html/