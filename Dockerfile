# Dockerfile for Laravel 10 on Render
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    mariadb-client \
    && docker-php-ext-install pdo_mysql mbstring zip

# Enable Apache mod_rewrite (required for Laravel routes)
RUN a2enmod rewrite

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 10000 (Render uses $PORT)
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
