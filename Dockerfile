# Use the official PHP image with Apache
FROM php:8.3-apache

# Enable required PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install \
    intl \
    pdo \
    pdo_mysql \
    zip

# Enable Apache mod_rewrite (for Symfony routing)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer.lock and composer.json
COPY composer.json composer.lock ./

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the project files
COPY . .

# Update the Apache configuration to use Symfony's public directory as DocumentRoot
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Ensure correct directory permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 for the web server
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]