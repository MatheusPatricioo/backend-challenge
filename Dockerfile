FROM php:8.3-cli

# git/unzip: used by Composer to download packages
# sqlite3: CLI to inspect the database from inside the container
RUN apt-get update \
&& apt-get install -y --no-install-recommends git unzip sqlite3 \
&& rm -rf /var/lib/apt/lists/*

# Fail the build early if the required extensions are missing
RUN php -m | grep -q pdo_sqlite && php -m | grep -q sqlite3

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

EXPOSE 8000