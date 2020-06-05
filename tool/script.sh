#!/bin/bash
docker run --rm myphp -r "echo 'Hello from docker myphp, you said $1';"
echo
cp /composer.json /composer.lock /magento
docker run --rm -v mage:/magento myphp /composer.phar install -d /magento
docker run --rm -v mage:/magento alpine/git clone https://github.com/nathanjosiah/html-sample.git /magento/magento-ce
