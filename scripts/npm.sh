#!/bin/bash

basepath="/home/www-data/scripts/npm"

fullpath=$basepath"/production.sh"

if [ "$APPLICATION_ENV" = 'development' ]; then
    fullpath=$basepath"/development.sh"
fi

$fullpath
