FROM richarvey/nginx-php-fpm:latest

WORKDIR /var/www/html

COPY . .

RUN apk add --no-cache nodejs npm

RUN composer install --no-dev --optimize-autoloader

RUN npm install
RUN npm run build

RUN php artisan storage:link || true
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

ENV WEBROOT=/var/www/html/public