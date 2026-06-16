FROM php:8.3-apache

WORKDIR /var/www/html

# Enable Apache rewrite (required for MVC routing)
RUN a2enmod rewrite

# Set correct DocumentRoot to /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Install system dependencies (safe baseline for most PHP apps)
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Fix permissions (VERY important for logs/uploads/cache)
RUN mkdir -p writable/logs writable/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 writable

EXPOSE 80

CMD ["apache2-foreground"]