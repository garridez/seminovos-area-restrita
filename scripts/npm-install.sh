#!/bin/bash
echo "npm install..."
apt-get install libpng-dev -y
npm set progress=false
npm install
