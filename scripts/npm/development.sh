#!/bin/bash

echo "Copying node_modules..."

if [ ! -d node_modules ]; then
    cp -R /var/node_modules node_modules
fi

npm run watch &
