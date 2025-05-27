#!/bin/bash

if [ "$APPLICATION_ENV" = 'development' ]; then
    (yarn; yarn run watch)&
fi
