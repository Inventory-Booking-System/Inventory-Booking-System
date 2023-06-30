FROM php:8.1-apache
ENV PATH="${PATH}:/sbin"
RUN apt update -y; \
    apt install zip -y; \
    curl https://github.com/Inventory-Booking-System/Inventory-Booking-System/releases/latest/download/Inventory-Booking-System.zip --output Inventory-Booking-System.zip; \
    unzip Inventory-Booking-System.zip -d /var/www/html/