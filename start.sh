#!/bin/bash

# Install composer dependencies
composer install

# Wait for database
echo "Waiting for database..."
until php -r "new PDO('mysql:host=db;dbname=laravel_ecommerce', 'laravel', 'password');" 2>/dev/null; do
    sleep 2
done

# Run migrations if needed
if ! php -r "\$pdo = new PDO('mysql:host=db;dbname=laravel_ecommerce', 'laravel', 'password'); \$stmt = \$pdo->query('SHOW TABLES LIKE \"users\"'); exit(\$stmt->rowCount() > 0 ? 0 : 1);" 2>/dev/null; then
    echo "Running migrations..."
    php artisan migrate --force
fi

# Create admin user if not exists
if ! php -r "\$pdo = new PDO('mysql:host=db;dbname=laravel_ecommerce', 'laravel', 'password'); \$stmt = \$pdo->prepare('SELECT COUNT(*) FROM users WHERE role = 2'); \$stmt->execute(); exit(\$stmt->fetchColumn() > 0 ? 0 : 1);" 2>/dev/null; then
    echo "Creating admin user..."
    php artisan admin:create "Admin User" "admin@example.com" "admin123"
fi

# Add countries if not exists
if ! php -r "\$pdo = new PDO('mysql:host=db;dbname=laravel_ecommerce', 'laravel', 'password'); \$stmt = \$pdo->query('SELECT COUNT(*) FROM countries'); exit(\$stmt->fetchColumn() > 0 ? 0 : 1);" 2>/dev/null; then
    echo "Adding countries..."
    php artisan tinker --execute="App\Models\Country::create(['name' => 'India', 'code' => 'IN']);"
fi

# Add shipping charges if not exists
if ! php -r "\$pdo = new PDO('mysql:host=db;dbname=laravel_ecommerce', 'laravel', 'password'); \$stmt = \$pdo->query('SELECT COUNT(*) FROM shipping_charges'); exit(\$stmt->fetchColumn() > 0 ? 0 : 1);" 2>/dev/null; then
    echo "Adding shipping charges..."
    php artisan tinker --execute="App\Models\ShippingCharge::create(['country_id' => 1, 'amount' => 50]); App\Models\ShippingCharge::create(['country_id' => 'rest_of_world', 'amount' => 100]);"
fi

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