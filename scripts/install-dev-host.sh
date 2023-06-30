
if [ "$1" == "run" ]; then

    vendorDirPrev=$(composer config vendor-dir)

    if [ "$vendorDirPrev" == "vendor" ]; then
        echo 'fazer nada'
        exit 0
    fi
    composer config vendor-dir vendor

    composer install --ignore-platform-reqs --no-scripts
    #composer dump -o --apcu
    composer config vendor-dir "$vendorDirPrev"
       
fi
