FROM php:7.4-cli

RUN apt-get update && \
    apt-get install -y \
    libxml2-dev \
    unzip \
    git \
    zlib1g-dev \
    libzip-dev && \
    docker-php-ext-install soap zip

COPY . /omnipay-vindicia
WORKDIR /omnipay-vindicia

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN composer install
