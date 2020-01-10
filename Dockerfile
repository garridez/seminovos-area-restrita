FROM snbh-server-base:7.2-node

RUN docker-php-ext-enable redis

#COPY . .

RUN apt-get install -y zlib1g-dev libicu-dev g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN apt-get install gnupg -y && \
    curl -sL https://deb.nodesource.com/setup_12.x | bash - && \
    apt-get install -y nodejs libpng-dev

ARG SESSION_PHP_COOKIE_DOMAIN
ARG SESSION_PHP_SAVE_HANDLER
ARG SESSION_SAVE_PATH

RUN echo "session.cookie_domain=$SESSION_PHP_COOKIE_DOMAIN\n\
session.save_handler=$SESSION_PHP_SAVE_HANDLER\n\
session.save_path=\"$SESSION_SAVE_PATH\"" > /usr/local/etc/php/conf.d/snbh-session.ini

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log
COPY server/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY server/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf