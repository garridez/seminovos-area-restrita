#!/bin/bash


if [ "$APPLICATION_ENV" = 'development' ]; then
    composer dev-enable
else
    composer dev-disable
fi
