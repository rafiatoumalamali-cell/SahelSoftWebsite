FROM php:8.3-apache

WORKDIR /var/www/html

COPY . .

RUN apt-get update && apt-get install -y \
    composer \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

CMD ["apache2-foreground"]