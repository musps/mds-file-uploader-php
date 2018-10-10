FROM php:7.2.2-fpm

RUN apt-get update
RUN apt-get install -y zip unzip
RUN apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql sockets

# --- Composer.
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"
