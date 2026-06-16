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

# Remove default Nginx configs
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader

# Create PHP-FPM config
RUN cat > /usr/local/etc/php-fpm.d/socket.conf << 'EOF'
[www]
listen = /var/run/php-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
EOF

# Create Nginx config
RUN cat > /etc/nginx/conf.d/app.conf << 'EOF'
server {
listen 80 default_server;
server_name _;
root /var/www/html/public;
index index.php;

location / {
try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
fastcgi_pass unix:/var/run/php-fpm.sock;
fastcgi_index index.php;
fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
include fastcgi_params;
}
}
EOF

EXPOSE 80

CMD ["sh", "-c", "php-fpm && nginx -g \"daemon off;\""]