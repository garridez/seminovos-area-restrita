#!/bin/bash

if [ $APPLICATION_ENV == 'development' ]; then
    composer development-enable 
else
    composer development-disable
fi
