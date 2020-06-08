#!/bin/bash
echo -en "\033[0;33m"\
  && docker run --rm myphp -r "echo 'Hello from docker myphp, you said $1';" \
  && echo -e "\033[0m"

echo -e "\033[0;34m" Running composer install "\033[0m"
cp /composer.json /composer.lock /magento
docker run --rm -v mage:/magento myphp /composer.phar install -d /magento
docker run --rm -v mage:/magento alpine/git clone https://github.com/nathanjosiah/html-sample.git /magento/magento-ce
