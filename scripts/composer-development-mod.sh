#!/bin/bash


if [ "$APPLICATION_ENV" = 'development' ]; then
    composer development-enable 
    echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/snbh-opcache-dev.ini
else
    composer development-disable
fi
