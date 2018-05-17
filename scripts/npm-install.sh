#!/bin/bash
echo "npm install..."
(npm install && npm run watch) &
