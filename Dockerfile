FROM php:8.0.19-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

COPY . .

# Dependencies will be installed at runtime

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 775 /var/www/public/uploads

EXPOSE 9000

COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]