#!/bin/bash

if [[ ! -d /magento/magento-ce ]]; then
  echo -e "\033[0;34m" Installing Magento 2.3.4 package. "\033[0m"
  # docker run --rm -v mage:/magento alpine/git clone --depth 1 --branch  /magento/magento-ce
  mkdir /magento/.composer
  cat << COMPOSER > /magento/.composer/auth.json
  {
      "http-basic": {
          "repo.magento.com": {
              "username": "${COMPOSER_USERNAME}",
              "password": "${COMPOSER_PASSWORD}"
          }
      }
  }
COMPOSER

  docker run --rm \
    -v mage:/magento \
    -e COMPOSER_HOME=/magento/.composer \
    magento/magento-cloud-docker-php:7.2-cli-1.2 \
    composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition:2.3.4 /magento/magento-ce

  echo -e "\033[0;34m" Running highly unsafe permision change to 777 for everything for now. "\033[0m"
  chmod -R 777 /magento/magento-ce

  echo -e "\033[0;34m" Installing Magento "\033[0m"
  docker run --rm \
    --network cicd\
    -v mage:/magento magento/magento-cloud-docker-php:7.2-cli-1.2\
    php /magento/magento-ce/bin/magento setup:install \
    --admin-firstname=Nathan \
    --admin-lastname=Smith \
    --admin-email=nathsmit@adobe.com \
    --admin-user=admin \
    --admin-password=123123q \
    --base-url=http://magento/ \
    --db-host=db \
    --db-name=main \
    --db-user=root \
    --db-password=secretpw \
    --currency=USD \
    --timezone=America/Chicago \
    --language=en_US \
    --use-rewrites=1 \
    --backend-frontname='admin'

  docker run --rm --network cicd -v mage:/magento magento/magento-cloud-docker-php:7.2-cli-1.2\
    php /magento/magento-ce/bin/magento de:mo:set production
fi
