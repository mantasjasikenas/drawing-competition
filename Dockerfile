FROM php:7.2.24-apache 
RUN docker-php-ext-install mysqli

COPY ./php.ini-development /usr/local/etc/php/
COPY ./php.ini-production /usr/local/etc/php/

