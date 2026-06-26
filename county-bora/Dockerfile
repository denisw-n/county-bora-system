FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

# Install Node.js and npm (Alpine)
RUN apk add --no-cache nodejs npm

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install
RUN npm run build

# Laravel optimizations
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

CMD ["supervisord", "-n"]