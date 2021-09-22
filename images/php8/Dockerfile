FROM php:8.0

RUN curl -s http://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

RUN apt-get update
RUN apt-get install -y libzip-dev zip && docker-php-ext-install zip