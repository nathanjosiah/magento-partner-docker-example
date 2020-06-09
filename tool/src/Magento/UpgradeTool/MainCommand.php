<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MainCommand extends Command
{
    protected static $defaultName = 'test:run';

    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure()
    {
        $this->addArgument(
            'test-name',
            InputArgument::REQUIRED,
            'The name of the test to run'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $composerUsername = getenv('COMPOSER_USERNAME');
        $composerPassword = getenv('COMPOSER_PASSWORD');
        $userPassword = getenv('ADMIN_PASSWORD');
        $adminEmail = getenv('ADMIN_EMAIL');

        if (!file_exists('/magento/magento-ce/vendor')) {
            $this->log('Installing Magento 2.3.4 package.');
            mkdir('/magento/.composer');

            file_put_contents('/magento/.composer/auth.json',
                <<<COMPOSER
  {
      "http-basic": {
      "repo.magento.com": {
          "username": "${composerUsername}",
              "password": "${composerPassword}"
          }
      }
  }
COMPOSER
            );
            $this->runPhp('composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition:2.3.4 /magento/magento-ce');

            $this->log('Running highly unsafe permision change to 777 for everything for now.');
            `chmod -R 777 /magento/magento-ce`;
            $this->log('Installing Magento');

            $this->runPhp('php /magento/magento-ce/bin/magento setup:install \
            --admin-firstname=Nathan \
            --admin-lastname=Smith \
            --admin-email=' . $adminEmail . ' \
            --admin-user=admin \
            --admin-password=' . $userPassword . ' \
            --base-url=http://magento/ \
            --db-host=db \
            --db-name=main \
            --db-user=root \
            --db-password=secretpw \
            --currency=USD \
            --timezone=America/Chicago \
            --language=en_US \
            --use-rewrites=1 \
            --backend-frontname=\'admin\'
            ');

            $this->runPhp('php /magento/magento-ce/bin/magento de:mo:set production');
        }

        $this->log('Accessing magento');

        $return = `docker run --network cicd --rm curlimages/curl -s http://magento/magento_version/`;
        $this->log((string)$return, 'yellow');

        $this->log('Testing guest cart creation');
        $return = `docker run --network cicd --rm curlimages/curl -s -X POST http://magento/rest/V1/guest-carts`;
        $this->log((string)$return, 'yellow');

        $this->log('Setting up MFTF');
        file_put_contents('/magento/magento-ce/dev/tests/acceptance/.env', <<<ENV
MAGENTO_BASE_URL=http://magento/
MAGENTO_BACKEND_NAME=admin
MAGENTO_ADMIN_USERNAME=admin
MAGENTO_ADMIN_PASSWORD=$userPassword
BROWSER=chrome
MODULE_WHITELIST=Magento_Framework,Magento_ConfigurableProductWishlist,Magento_ConfigurableProductCatalogSearch
DEFAULT_TIMEZONE=America/Chicago
SELENIUM_HOST=selenium
ENV
        );
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf reset --hard');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf build:project');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/admin_account_sharing 1');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/use_form_key 0');
        $this->log('Fixing bad composer requirement for 2.3.4');
        $this->runPhp('composer require -d /magento/magento-ce symfony/http-foundation ^4.0');

        $this->log('Running Selenium');
        `docker run --name selenium --rm -d --network cicd -p 4444:4444 -p 5900:5900 -v /dev/shm:/dev/shm selenium/standalone-chrome-debug:3.141.59-gold`;

        $this->log('Running AdminLoginTest');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf run:test AdminLoginTest');

        return 0;
    }

    private function log(string $string, string $color = 'blue', bool $newline = true): void
    {
        $this->output->writeln('<fg=' . $color . '>' . $string . '</>', OutputInterface::VERBOSITY_NORMAL);
    }
    private function runPhp(string $command): void
    {
        $full = 'docker run --rm \
            --network cicd\
            -e COMPOSER_HOME=/magento/.composer\
            -v mage:/magento magento/magento-cloud-docker-php:7.2-cli-1.2\
            ' . $command;
        $this->log('Running:' . $full, 'white');
        // passthru will stream the output in real time without modification
        passthru($full);
    }
}
