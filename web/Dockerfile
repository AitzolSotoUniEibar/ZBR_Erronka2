FROM php:8.2-apache
COPY . /var/www/html/
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-install exif
EXPOSE 80