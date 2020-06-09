#!/bin/bash

info () {
  echo -e "\033[0;34m" $* "\033[0m"
}

DIND_CONTAINER=$(docker run -d --privileged -p 12375:2375 -p 8877:80 -e DOCKER_TLS_CERTDIR="" docker:dind)
export DOCKER_HOST=tcp://localhost:12375

info 'Building Tool'
docker build -t tool ./tool
info 'Building PHP CLI'
docker build -t php-cli ./php-cli
info 'Building PHP FPM'
docker build -t mage-php-fpm ./php-fpm
info 'Building Nginx'
docker build -t magento ./nginx
info 'Building Mariadb'
docker build -t db ./db

info 'Create volume'
docker volume create --name mage

info 'Create network'
docker network create --driver bridge cicd

info 'Running php-fpm'
PHP_CONTAINER=$(docker run --rm -d \
  -p 9000:9000 \
  --name fpm \
  --network cicd \
  -v mage:/themount\
  -w=/themount/magento-ce\
  mage-php-fpm)

info 'Running mariadb'
docker run --rm -d \
  -p 3306:3306 \
  --name db \
  --network cicd \
  -e MYSQL_ROOT_PASSWORD=secretpw \
  -e 'MYSQL_ROOT_HOST=%' \
  -e MYSQL_DATABASE=main \
  db

info 'Running Tool'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  somearg

info 'Running Nginx'
MAGENTO_CONTAINER=$(docker run --rm -d \
  --network cicd \
  --name magento \
  -v mage:/themount magento
)

info 'Accessing magento'
echo -en "\033[0;33m"\
  && docker run --network cicd --rm curlimages/curl -s http://magento/magento_version/ \
  && echo -e "\033[0m"

info 'Getting a guest cart'
echo -en "\033[0;33m"\
  && docker run --network cicd --rm curlimages/curl -s -X POST http://magento/rest/V1/guest-carts \
  && echo -e "\033[0m"

info 'Setting up mftf'
docker run --rm --network cicd -v mage:/magento magento/magento-cloud-docker-php:7.2-cli-1.2\
    php /magento/magento-ce/vendor/bin/mftf reset --hard
docker cp $(pwd)/mftf.env magento:/themount/magento-ce/dev/tests/acceptance/.env
docker run --rm --network cicd -v mage:/magento magento/magento-cloud-docker-php:7.2-cli-1.2\
    php /magento/magento-ce/vendor/bin/mftf build:project
docker run --rm --network cicd -v mage:/magento magento/magento-cloud-docker-php:7.2-cli-1.2\
    php /magento/magento-ce/bin/magento config:set admin/security/admin_account_sharing 1
docker run --rm --network cicd -v mage:/magento magento/magento-cloud-docker-php:7.2-cli-1.2\
    php /magento/magento-ce/bin/magento config:set admin/security/use_form_key 0
info 'Fixing bad composer requirement for 2.3.4'
docker run --rm\
  -v mage:/magento\
  -e COMPOSER_HOME=/magento/.composer\
  magento/magento-cloud-docker-php:7.2-cli-1.2\
  composer require -d /magento/magento-ce symfony/http-foundation ^4.0
info 'Running Selenium'
docker run --name selenium --rm -d --network cicd -p 4444:4444 -p 5900:5900 -v /dev/shm:/dev/shm selenium/standalone-chrome-debug:3.141.59-gold

info 'Running AdminLoginTest'
docker run --rm --network cicd -v mage:/magento magento/magento-cloud-docker-php:7.2-cli-1.2\
    php /magento/magento-ce/vendor/bin/mftf run:test AdminLoginTest
