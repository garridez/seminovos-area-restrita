FROM snbh-server-base:8.2-node

RUN docker-php-ext-enable redis

#COPY . .
VOLUME /home/www-data/node_modules

RUN apt-get install -y zlib1g-dev libicu-dev g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl


ARG SESSION_PHP_COOKIE_DOMAIN
ARG SESSION_PHP_SAVE_HANDLER
ARG SESSION_SAVE_PATH

RUN echo "session.cookie_domain=$SESSION_PHP_COOKIE_DOMAIN\n\
session.save_handler=$SESSION_PHP_SAVE_HANDLER\n\
session.save_path=\"$SESSION_SAVE_PATH\"" > /usr/local/etc/php/conf.d/snbh-session.ini

RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/snbh-memory.ini
RUN echo "max_input_vars=10000" > /usr/local/etc/php/conf.d/snbh-input_vars.ini

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log
COPY server/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY server/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf