#!/bin/bash


if [ "$APPLICATION_ENV" = 'development' ]; then
    composer dev-enable
    mkdir -p ../data/cache
    # Pequeno hack para melhorar a performance no volume do Docker no Windows
    (
        composer config vendor-dir ../vendor;
        composer install;
        composer config vendor-dir vendor;
        echo 'ok'
    )&

else
    composer dev-disable
fi
