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

class RunTest extends AbstractCommand
{
    protected static $defaultName = 'test:run';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(
            'test-name',
            InputArgument::REQUIRED,
            'The name of the test to run'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
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
}
