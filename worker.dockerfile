FROM php:8.3-fpm 

#ENV SERVER_NAME=":80"

WORKDIR /app

COPY --chown=www-data:www-data . /app

    #RUN apt update && apt install zip libzip-dev -y && \ docker-php-ext-install zip pcntl && \ docker-php-ext-enable zip

    RUN apt-get update && apt-get install -y libzip-dev zip unzip curl \
    && docker-php-source extract \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pcntl pdo pdo_mysql \
    && docker-php-source delete
    
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer 

RUN  composer install && \
    composer require laravel/octane && \
    php artisan octane:install --server=frankenphp

EXPOSE 8000  
CMD php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000 