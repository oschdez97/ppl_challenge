FROM php:7.3-apache

RUN apt -y update \
  && apt install -y \
  mariadb-client

# pdo_mysql package
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli

RUN curl https://phar.phpunit.de/phpunit.phar -L -o phpunit.phar
RUN chmod +x phpunit.phar
RUN mv phpunit.phar /usr/local/bin/phpunit