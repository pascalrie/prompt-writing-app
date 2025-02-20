# Dockerfile
FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy project files
COPY . .

# Install Symfony dependencies
RUN composer install --no-scripts --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/symfony

CMD ["php-fpm", "-F"]

EXPOSE 9000
