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

class RunTest extends Command
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
        $userPassword = getenv('ADMIN_PASSWORD');

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
