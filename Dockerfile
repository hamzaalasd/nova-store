FROM richarvey/nginx-php-fpm:latest

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan storage:link || true
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

ENV WEBROOT=/var/www/html/public