FROM snbh-server-base:7.2

RUN apt-get install gnupg -y && \
    curl -sL https://deb.nodesource.com/setup_9.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm

COPY server/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY server/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf
