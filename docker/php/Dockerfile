FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
