#!/bin/bash

# Install composer dependencies
composer install

# Wait for database
echo "Waiting for database..."
until php -r "new PDO('mysql:host=db;dbname=laravel_ecommerce', 'laravel', 'password');" 2>/dev/null; do
    sleep 2
done

# Run migrations
echo "Running migrations..."
php artisan migrate:fresh --force
echo "Migrations completed!"

# Create admin user
echo "Creating admin user..."
php artisan admin:create "Admin User" "admin@example.com" "admin123" || true

# Add countries
echo "Adding countries..."
php artisan tinker --execute="App\Models\Country::firstOrCreate(['code' => 'IN'], ['name' => 'India']);" || true

# Add shipping charges
echo "Adding shipping charges..."
php artisan tinker --execute="App\Models\ShippingCharge::firstOrCreate(['country_id' => 1], ['amount' => 50]); App\Models\ShippingCharge::firstOrCreate(['country_id' => 'rest_of_world'], ['amount' => 100]);" || true

# Set permissions
chown -R www-data:www-data /var/www/public/uploads
chmod -R 775 /var/www/public/uploads

echo "Setup complete!"

# Start PHP-FPM in background
php-fpm &

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000

# Keep container running
wait