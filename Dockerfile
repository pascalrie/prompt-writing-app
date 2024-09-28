FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    zlib1g-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    zlib1g-dev \
    libevent-dev \
    zip \
    unzip \
    git \
    sudo \
    && docker-php-ext-install intl pdo_mysql zip pdo zip mbstring opcache
RUN php -i
RUN pecl install pecl_http \
    && docker-php-ext-enable http \

ENV APACHE_DOCUMENT_ROOT /var/www/html/public


RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite headers
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN sudo a2enmod ext-http
RUN composer install --no-interaction --optimize-autoloader --no-dev

RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log /var/www/html/var/sessions \
    && chown -R www-data:www-data /var/www/html/var

ENV DATABASE_URL="mysql://root:root@db:3308/symfony?serverVersion=mariadb-10.6"

WORKDIR /var/www/html

EXPOSE 81

CMD ["apache2-foreground"]