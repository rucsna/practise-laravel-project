FROM php:8.1 as php

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev
RUN docker-php-ext-install pdo pdo_mysql bcmath

WORKDIR /var/www
COPY . .

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

ENV PORT=8000
ENTRYPOINT [ "/usr/local/bin/wait-for-it.sh", "database:3306", "--", "docker/entrypoint.sh" ]
