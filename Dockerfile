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

# Copia somente arquivos do Composer para aproveitar cache do Docker
COPY composer.json composer.lock* ./

# Roda o composer install durante o build
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copia o restante do código da aplicação
COPY . .
