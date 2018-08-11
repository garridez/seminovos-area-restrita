#!/bin/bash

echo 'aws ecr get-login...'
aws ecr get-login > aws_ect_login
echo $(sed 's/-e none//' aws_ect_login) > aws_ect_login
sh aws_ect_login
rm -rf aws_ect_login

tag=$(git describe)
echo $tag
          
docker tag area-restrita_snbh-area-restrita-production:latest 041122835851.dkr.ecr.us-west-2.amazonaws.com/area-restrita:latest
docker push 041122835851.dkr.ecr.us-west-2.amazonaws.com/area-restrita:latest
docker rmi -f 041122835851.dkr.ecr.us-west-2.amazonaws.com/area-restrita:latest




