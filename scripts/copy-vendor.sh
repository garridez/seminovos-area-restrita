#!/bin/bash
if [ "$APPLICATION_ENV" == "development" ]; then
    "$curDir/_copy-vendor.sh" run &
fi
    