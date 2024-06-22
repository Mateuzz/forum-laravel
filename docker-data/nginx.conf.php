<?php

$result = '
#user http;
worker_processes  1;

error_log  /var/log/nginx/error.log;
error_log  /var/log/nginx/notice.log notice;
error_log  /var/log/nginx/info.log info;

#pid        logs/nginx.pid;


events {
    worker_connections  1024;
}

http {
    types_hash_max_size 4096;
    include       mime.types;
    default_type  application/octet-stream;

    #log_format  main  \'$remote_addr - $remote_user [$time_local] "$request" \'

    #access_log  logs/access.log  main;

    sendfile        on;
    #tcp_nopush     on;

    #keepalive_timeout  0;
    keepalive_timeout  65;
    charset utf-8;

    #gzip  on;

    server {
        listen       80;
        server_name  api.localhost;
        root /usr/src/app/public;

        error_page 404 /index.php;

        #charset koi8-r;
        #access_log  logs/host.access.log  main;

        location / {
            if ($request_method = \'OPTIONS\') {
                add_header \'Access-Control-Allow-Origin\' \''.$_ENV['CORS_URL'].'\';
                add_header \'Access-Control-Allow-Methods\' \'GET,POST,OPTIONS,PUT,PATCH,DELETE\';
                add_header \'Access-Control-Allow-Headers\' \'*\';
                add_header \'Access-Control-Allow-Credentials\' \'true\';
                add_header \'Access-Control-Max-Age\' 86400;
                add_header \'Content-Length\' 0;
                add_header \'Content-Type\' \'text/plain\';
                return 204;
            }

            # if ($request_method != \'OPTIONS\') {
            add_header \'Access-Control-Allow-Origin\' \''.$_ENV['CORS_URL'].'\';
            add_header \'Access-Control-Allow-Methods\' \'GET,POST,OPTIONS,PUT,PATCH,DELETE\';
            add_header \'Access-Control-Allow-Headers\' \'*\';
            add_header \'Access-Control-Allow-Credentials\' \'true\';
            # }

            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico {
            access_log off;
            log_not_found off;
        }

        location = /robots.txt {
            access_log off;
            log_not_found off;
        }

        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_param  SCRIPT_FILENAME ${document_root}$fastcgi_script_name;
            include        fastcgi_params;
        }

        location ~ /\.ht {
            deny  all;
        }
    }
}
';

$file = fopen(__DIR__ . "/nginx.conf.gen", "w");
fwrite($file, $result, strlen($result));
fclose($file);

?>
