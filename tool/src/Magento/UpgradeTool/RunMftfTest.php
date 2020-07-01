<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Environment\EnvironmentManager;
use Magento\UpgradeTool\Executor\Php;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunMftfTest extends AbstractCommand
{
    protected static $defaultName = 'verify:mftf';
    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    public function __construct(
        Php $phpExecutor,
        LoggerInterface $logger,
        EnvironmentManager $environmentManager,
        string $name = null
    ) {
        parent::__construct($phpExecutor, $logger, $name);
        $this->environmentManager = $environmentManager;
    }


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
        $userPassword = getenv('MAGE_ADMIN_PASSWORD');
        $testName = $input->getArgument('test-name');
        $phpVersion = $input->getOption('php');

        $this->log('Setting up MFTF');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf reset --hard');
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
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf build:project');

        $this->environmentManager->startSelenium($phpVersion);

        $this->log('Running MFTF test ' . $testName . ' using PHP ' . $phpVersion);
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf run:test ' . $testName);


        return 0;
    }
}
