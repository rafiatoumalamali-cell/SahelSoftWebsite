FROM php:8.3-apache

WORKDIR /var/www/html

COPY . .

# Disable conflicting MPM modules and enable only prefork
RUN a2dismod mpm_worker mpm_event || true && \
    a2enmod mpm_prefork

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

CMD ["apache2-foreground"]