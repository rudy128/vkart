# Laravel E-commerce Application

A full-featured e-commerce platform built with Laravel, featuring product management, shopping cart, order processing, and admin panel.

## Features

### Frontend
- Product catalog with categories and brands
- Shopping cart functionality
- User authentication and profiles
- Order tracking and history
- Wishlist management
- Product reviews and ratings
- Discount coupons
- Responsive design

### Admin Panel
- Product management (CRUD operations)
- Category and brand management
- Order management and status updates
- User management
- Discount coupon management
- Shipping charge configuration
- Product image upload with thumbnails
- Dashboard with analytics

## Tech Stack

- **Backend**: Laravel 9.x, PHP 8.0
- **Database**: MySQL 8.0
- **Frontend**: Bootstrap, jQuery
- **Image Processing**: Intervention Image
- **Shopping Cart**: Laravel Shopping Cart
- **Containerization**: Docker & Docker Compose

## Quick Start

### Prerequisites
- Docker and Docker Compose
- Git

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd laravel-ecommerce
```

2. **Start the application**
```bash
docker-compose up -d
```

3. **Install dependencies and setup**
```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

4. **Create admin user**
```bash
docker-compose exec app php artisan admin:create "Admin User" "admin@example.com" "admin123"
```

5. **Access the application**
- **Frontend**: http://localhost:8080
- **Admin Panel**: http://localhost:8080/admin/login

## Default Credentials

### Admin Login
- **Email**: admin@example.com
- **Password**: admin123

## Configuration

### Environment Variables
Copy `.env.example` to `.env` and configure:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=laravel
DB_PASSWORD=password
```

### File Permissions
Ensure proper permissions for uploads:
```bash
docker-compose exec app chmod -R 775 /var/www/public/uploads
docker-compose exec app chown -R www-data:www-data /var/www/public/uploads
```

## Production Deployment

### With External Nginx
The application exposes PHP-FPM on port 9000 for production use with external nginx:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/laravel-ecommerce/public;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass localhost:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

## API Endpoints

### Public Routes
- `GET /` - Homepage
- `GET /shop` - Product catalog
- `GET /product/{slug}` - Product details
- `POST /add-to-cart` - Add to cart
- `GET /checkout` - Checkout page

### Admin Routes (Protected)
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/products` - Product management
- `GET /admin/orders` - Order management
- `GET /admin/users` - User management

## Database Schema

### Key Tables
- `users` - User accounts (role: 1=customer, 2=admin)
- `products` - Product catalog
- `categories` - Product categories
- `orders` - Customer orders
- `order_items` - Order line items
- `countries` - Shipping countries
- `shipping_charges` - Shipping rates

## Development

### Adding New Features
1. Create migrations: `php artisan make:migration create_table_name`
2. Create models: `php artisan make:model ModelName`
3. Create controllers: `php artisan make:controller ControllerName`

### Running Tests
```bash
docker-compose exec app php artisan test
```

### Debugging
View logs:
```bash
docker-compose exec app tail -f /var/www/storage/logs/laravel.log
```

## Troubleshooting

### Common Issues

**Image upload fails**
```bash
docker-compose exec app chmod -R 775 /var/www/public/uploads
```

**Database connection error**
```bash
docker-compose exec app php artisan config:cache
```

**Permission denied**
```bash
docker-compose exec app chown -R www-data:www-data /var/www
```

## Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -am 'Add feature'`
4. Push to branch: `git push origin feature-name`
5. Submit pull request

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For support and questions, please open an issue in the repository.