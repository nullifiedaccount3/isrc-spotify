FROM php:7.1-fpm
RUN apt-get update
RUN apt-get install -y --no-install-recommends git zip libpng-dev libicu-dev g++ libxml2-dev libmcrypt-dev libxslt-dev libzip-dev
RUN docker-php-ext-install bcmath intl gd soap mcrypt pdo_mysql xsl zip
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer
COPY . /code
WORKDIR /code
RUN composer install --no-dev
RUN php artisan migrate
