#!/bin/sh

function setup() {
     php ./docker-data/nginx.conf.php && mv ./docker-data/nginx.conf.gen /etc/nginx/nginx.conf
}

function start() {
    php-fpm82
    nginx
    echo "-- Server started --\n"
    exec sh
}

setup

start
