FROM php:8.2-fpm

WORKDIR /app

RUN apt-get update && apt-get install -y apt-utils vim curl net-tools dialog zip bash git libicu-dev g++ libzip-dev zip && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql zip

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN usermod -u 1000 www-data

EXPOSE 9000
