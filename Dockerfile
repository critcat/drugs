FROM php:7.4-fpm-alpine

RUN apk --no-cache update && apk --no-cache add bash

RUN docker-php-ext-install pdo_mysql

#installer composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php && php -r "unlink('composer-setup.php');" && mv composer.phar /usr/local/bin/composer

#installer cli symfony
RUN wget https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/html