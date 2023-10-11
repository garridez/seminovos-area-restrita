#!/bin/bash

if [ ! -d node_modules ]; then
    echo 'npm install...';
    npm install
fi

apt update
apt-get install iputils-ping -y
ping -c 1 docker.for.win.localhost &>/dev/null

if [ $? -eq 0 ]; then
    npm run dev &
    echo 'Não executar o "npm run watch" quando o host for windows'
else
    echo  'npm run watch...';
    npm run watch &
fi;