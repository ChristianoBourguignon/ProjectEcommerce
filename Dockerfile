FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    git \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN docker-php-ext-install pdo_mysql


WORKDIR /var/www/html
