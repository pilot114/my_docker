FROM php:7.2-fpm

RUN pecl install xdebug \
&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY php.ini /usr/local/etc/php/
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
