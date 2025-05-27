#!/bin/bash

if [ "$APPLICATION_ENV" = 'development' ]; then
    git config --global --add safe.directory /home/www-data
    echo -n "$(git describe --tags)" > version
fi
