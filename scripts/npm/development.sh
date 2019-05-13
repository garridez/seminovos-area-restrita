#!/bin/bash

if [ ! -d node_modules ]; then
    echo 'npm install...';
    npm install
fi

echo  'npm run watch...';
npm run watch &
