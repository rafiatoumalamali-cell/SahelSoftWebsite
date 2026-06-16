FROM php:8.3-apache

WORKDIR /var/www/html

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    unzip git curl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader || true

COPY . .

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN mkdir -p writable/logs writable/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 writable

EXPOSE 80

CMD ["apache2-foreground"]