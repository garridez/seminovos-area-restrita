FROM snbh-server-base:7.2

RUN apt-get install gnupg -y && \
    curl -sL https://deb.nodesource.com/setup_8.x | bash - && \
    apt-get install -y nodejs libpng-dev
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && apt-get install yarn

RUN pecl install apcu && docker-php-ext-enable apcu opcache

VOLUME /root/.npm

COPY package*.json ./
COPY yarn.lock ./yarn.lock

RUN yarn install

COPY server/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY server/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf