# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy your entire project into the container
COPY . .

# Expose the default Apache port
EXPOSE 80

