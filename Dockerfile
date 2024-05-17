FROM alpine:3.19

WORKDIR /usr/src/app

ENV CORS_URL=http://vue.localhost:80

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

COPY . .
RUN mv ./docker-data/php.ini /etc/php82/ \
    && chmod +x ./server.sh \
    && composer run post-autoload-dump \
    && adduser -D -H www \
    && mv ./docker-data/www.conf /etc/php82/php-fpm.d/www.conf \
    # && chmod 755 -R ./bootstrap \
    # && chmod 755 -R ./storage \
    && chown -R www ./storage \
    && chown -R www ./bootstrap \
    && composer dump-autoload --optimize \
    && php artisan key:generate \
    && php artisan optimize \
    && php artisan storage:link
    # mkdir /var/run/php-fpm && \

        # composer dump-autoload --optimize && \
        # php artisan optimize && \
        # php artisan db:migrate --seed

# COPY ./docker-data/php.ini /etc/php/php.ini
# COPY ./docker-data/nginx.conf /etc/nginx/nginx.conf

# MOUNT volumes/laravel/logs:/usr/src/app/storage/logs
# MOUNT volumes/nginx/logs:/var/logs/nginx

CMD ["./server.sh"]
