FROM php:7.4-cli-alpine

RUN apk update && apk add composer

ADD . /app

WORKDIR /app

RUN chown www-data -R /app && chgrp www-data -R /app

USER www-data

RUN composer install

USER root

CMD [ "php", "./bin/zeit-buchung.php" ]
