#!/bin/bash
service=$1
version=$2

if [[ "$service" = "selenium" ]]; then
  docker run --rm -d \
    -p 4444:4444 -p 5900:5900 \
    --name selenium \
    --network cicd \
    --link magento-php$(echo -n $version | sed 's/\.//'):magento \
    -v /dev/shm:/dev/shm \
    selenium/standalone-chrome-debug:3.141.59-gold
elif [[ "$service" = "php" ]]; then
  docker run --rm -d \
    --name fpm-$(echo -n $version | sed 's/\.//') \
    --network cicd \
    -v mage:/magento\
    -w=/magento/magento-ce\
    magento/magento-cloud-docker-php:${version}-fpm-1.2
elif [[ "$service" = "mysql" ]]; then
  docker run --rm -d \
    --name db \
    --network cicd \
    -e MYSQL_ROOT_PASSWORD=secretpw \
    -e 'MYSQL_ROOT_HOST=%' \
    -e MYSQL_DATABASE=main \
    db
elif [[ "$service" = "elasticsearch" ]]; then
  docker run --rm -d \
    --name elasticsearch${version} \
    --network cicd \
    -e "discovery.type=single-node" \
    magento/magento-cloud-docker-elasticsearch:${version}-1.2
elif [[ "$service" = "nginx" ]]; then
  # TODO add names params for extra info like phpversion to distinguish nginx version from phpversion
  docker run --rm -d \
    --network cicd \
    --name magento-php$(echo -n $version | sed 's/\.//') \
    -e FPM_HOST=fpm-$(echo -n $version | sed 's/\.//')\
    -v mage:/magento magento
fi
