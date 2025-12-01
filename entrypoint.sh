#!/bin/bash

sleep 5

echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear caches
#php artisan key:generate
php artisan config:cache

# Run migrations
php artisan migrate --force

# Publish Swagger assets
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --force

# Run seeders
php artisan db:seed --class=BookSeeder --force
php artisan db:seed --class=BookBorrowingSeeder --force

# Remove or comment this line - avoid recursion
# entrypoint.sh

# Don't run tests here in production (optional)
# php artisan test
# Start Apache
apache2-foreground
