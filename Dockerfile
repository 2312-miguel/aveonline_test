# Base image for PHP
FROM php:8.2-fpm

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Install PHP dependencies using Composer
RUN composer install

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port for the development server
EXPOSE 8000

# Start the Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
