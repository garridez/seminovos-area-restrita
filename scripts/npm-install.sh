#!/bin/bash
echo "npm install..."
npm set progress=false
npm install
npm run watch &
