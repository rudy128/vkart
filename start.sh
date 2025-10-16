#!/bin/bash

# Install composer dependencies
composer install

# Start PHP-FPM in background
php-fpm &

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000

# Keep container running
wait