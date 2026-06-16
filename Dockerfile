FROM php:8.3-apache

WORKDIR /var/www/html

RUN a2enmod rewrite

# 🔥 FIX: disable ALL MPMs first
RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2dismod mpm_prefork || true

# 🔥 enable ONLY prefork (required for mod_php)
RUN a2enmod mpm_prefork

RUN apt-get update && apt-get install -y \
    git curl unzip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader || true

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]