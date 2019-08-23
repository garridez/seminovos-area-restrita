FROM snbh-server-base:7.2-node

RUN docker-php-ext-enable redis

#COPY . .

ARG SESSION_PHP_SAVE_HANDLER
ARG SESSION_SAVE_PATH
# No local não salva a sessão no redis
RUN echo ";session.cookie_domain=localhost\n\
session.save_handler=$SESSION_PHP_SAVE_HANDLER\n\
session.save_path=\"$SESSION_SAVE_PATH\"" > /usr/local/etc/php/conf.d/snbh-session.ini

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log
COPY server/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY server/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf