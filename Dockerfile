FROM php:8.1-cli

WORKDIR /usr/src/app/app

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

RUN cd ~  \
  && curl -sS https://getcomposer.org/installer -o composer-setup.php \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug