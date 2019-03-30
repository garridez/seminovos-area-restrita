FROM snbh-server-base:7.2-node

COPY server/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY server/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf