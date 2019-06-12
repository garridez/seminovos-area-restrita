FROM snbh-server-base:7.2-node

RUN yes '' | pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

arg SESSION_PHP_SAVE_HANDLER
arg SESSION_SAVE_PATH

RUN echo "session.save_handler=$SESSION_PHP_SAVE_HANDLER\n\
session.save_path=\"$SESSION_SAVE_PATH\"" > /usr/local/etc/php/conf.d/snbh-session-redis.ini

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log
COPY server/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY server/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf