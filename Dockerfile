FROM snbh-server-base:7.2

RUN apt-get install gnupg -y && \
    curl -sL https://deb.nodesource.com/setup_8.x | bash - && \
    apt-get install -y nodejs libpng-dev

RUN pecl install apcu && docker-php-ext-enable apcu opcache

VOLUME /root/.npm

COPY package*.json ./

# Soluciona o problema do NPM com o volume do Docker
RUN echo "npm install..." && npm install && \
    echo "Moving node_modules to /var/node_modules" && \
    mv node_modules /var/node_modules

COPY server/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY server/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf