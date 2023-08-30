

if [ "$1" == "run" ]; then
    if [ -d "../vendor" ]; then
        pwd
        vendorDirPrev=$(composer config vendor-dir)
        
        echo "$vendorDirPrev"

        if [ "$vendorDirPrev" == "vendor" ]; then
            echo 'fazer nada'
            exit 0
        fi

        echo 'Copiando vendor..'
        #cp -R ../vendor .
        
        echo 'ok'
        composer config vendor-dir vendor
        composer dump -o --apcu
        composer config vendor-dir "$vendorDirPrev"
        
    fi
fi
