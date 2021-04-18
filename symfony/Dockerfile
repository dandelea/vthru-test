FROM php:7.4-fpm-alpine

RUN apk update && apk add --update \
    make \
    curl

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions \
    mysqli \
    pdo_mysql
    
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
COPY ./ /var/www/symfony
WORKDIR /var/www/symfony/