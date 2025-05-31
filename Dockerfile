# Use the official PHP image with Apache
FROM php:7.4-apache

# Install the necessary PostgreSQL dependencies and PHP extensions
RUN apt-get update && apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql

# Copy your PHP files into the container's web directory
COPY . /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
