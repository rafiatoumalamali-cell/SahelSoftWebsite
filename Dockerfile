FROM php:8.3-apache

WORKDIR /var/www/html

# Enable rewrite ONLY
RUN a2enmod rewrite

# Remove ALL MPM conflicts safely BEFORE Apache starts
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load || true
RUN rm -f /etc/apache2/mods-enabled/mpm_worker.load || true
RUN rm -f /etc/apache2/mods-enabled/mpm_prefork.load || true

# Force ONLY prefork cleanly
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