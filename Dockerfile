# Use the official PHP image with Apache installed
FROM php:8.0-apache

# Install the MySQL driver for PHP (Crucial step!)
# The default PHP image doesn't talk to MySQL out of the box.
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy our application code into the container's web folder
COPY index.php /var/www/html/
