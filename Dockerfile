FROM php:8.3-fpm

WORKDIR /var/www/html

COPY . .

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    nginx \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader

# Configure Nginx
RUN mkdir -p /etc/nginx/conf.d && \
    echo 'server { \
    listen 80; \
    server_name _; \
    root /var/www/html; \
    index index.php; \
    location ~ \.php$ { \
    fastcgi_pass 127.0.0.1:9000; \
    fastcgi_index index.php; \
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    include fastcgi_params; \
    } \
    }' > /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g \"daemon off;\""]