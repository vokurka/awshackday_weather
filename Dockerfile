FROM php:7-cli

RUN apt-get update -q \
  && apt-get install -y \
  git \
  unzip 

RUN cd \
  && curl -sS https://getcomposer.org/installer | php \
  && ln -s /root/composer.phar /usr/local/bin/composer

COPY php.ini /usr/local/etc/php/php.ini

RUN mkdir /data
RUN mkdir /data/out
RUN mkdir /data/out/tables
RUN mkdir /data/in
RUN mkdir /data/in/tables

COPY . /home

COPY config.json /data/config.json

WORKDIR /home