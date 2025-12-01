# Dockerfile for Laravel 10 on Render / Railway
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

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Set ENV variables if needed (Optional)
# ENV APP_ENV=production

# Run Migrations, Seeders & Unit Tests
# Using entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port
EXPOSE 80

# Start Apache and run entrypoint
CMD ["/entrypoint.sh"]
