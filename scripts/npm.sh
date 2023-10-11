#!/bin/bash

if [ "$APPLICATION_ENV" = 'development' ]; then
    yarn
    npm run watch &
fi
