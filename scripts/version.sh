#!/bin/bash

if [ "$APPLICATION_ENV" = 'development' ]; then
    echo -n "$(git describe)" > version
fi
