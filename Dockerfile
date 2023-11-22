# Use the official PHP image as the base image
FROM php:8.2.12-apache

# Install the mysqli extension
RUN docker-php-ext-install mysqli

# Copy your application code to the container
COPY ./src /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Start the Apache web server
CMD ["apache2-foreground"]
