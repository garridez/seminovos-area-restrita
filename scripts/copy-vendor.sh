#!/bin/bash
if [ "$APPLICATION_ENV" == "development" ]; then
    "scripts/_copy-vendor.sh" run &
fi
    