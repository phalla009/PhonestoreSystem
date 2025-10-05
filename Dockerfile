FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y\
build-essential \
libpng-dev\
libjpeg-dev\
libonig-dev\
libxml2-dev\
zip\
unzip\
curl \
git \
vim\
libzip-dev \
sqlite3 \
libsqlite3-dev

RUN docker-php-ext-install pdo pdo

COPY --from-composer:latest /usr/bin/

COPY ./var/www/html
COPY --chown-www-data:www-data/var/www/ht
RUN chmod -R 755 /var/www/html

RUN cp.env.example.env

RUN composer install && php artisan key:generate

#Create data directory and empty sqlite file
RUN mkdir -p /var/www/html/database\
    && touch /var/www/html/database/database.sqlite
    
EXPOSE 8000
CMD php artisan serve-host-0.0.0.0-port-8000