FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/symfony

COPY . .

RUN composer install --no-scripts --optimize-autoloader

RUN chown -R www-data:www-data /var/www/symfony

CMD ["php-fpm", "-F"]

EXPOSE 9000
