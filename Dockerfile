FROM alpine:3.19

WORKDIR /usr/src/app

RUN apk update && apk add \
        curl \
        nginx \
        php-fpm \
\
        php \
        php-common \
        php-gd \
        php-pgsql \
        php-phar \
        php-openssl \
        php-iconv \
        php-session \
        php-tokenizer \
        php-ctype \
        php-curl \
        php-dom \
        php-json \
        php-fileinfo \
        php-opcache \
        php-mbstring \
        php-xml \
        php-pdo \
        php-pdo_pgsql \
        php-xmlwriter \
    \
        && curl https://getcomposer.org/installer -o composer-setup.php \
        && chmod +x composer-setup.php \
        && php composer-setup.php --filename=composer \
        && mv composer /bin \
        && rm composer-setup.php

COPY composer.* .
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts

ENV CORS_URL=http://localhost
ENV RUN_MIGRATIONS=false
ENV RUN_DB_SEED=false

COPY . .
RUN chmod +x ./server.sh \
    && composer run post-autoload-dump \
    && adduser -D -H www \
    && chown -R www ./storage \
    && chown -R www ./bootstrap \
    && composer dump-autoload --optimize \
    && php artisan key:generate \
    && php artisan storage:link && \
    apk cache clean && apk cache purge && rm -rf /var/cache/apk/*


EXPOSE 80

CMD ["./server.sh"]
