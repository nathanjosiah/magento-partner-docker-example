<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Check;

use Magento\UpgradeTool\Environment\EnvironmentManager;
use Magento\UpgradeTool\Executor\Php;
use Psr\Log\LoggerInterface;

/**
 *
 */
class Mftf
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Php
     */
    private $phpExecutor;
    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    /**
     * @param LoggerInterface $logger
     * @param Php $phpExecutor
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(
        LoggerInterface $logger,
        Php $phpExecutor,
        EnvironmentManager $environmentManager
    ) {
        $this->logger = $logger;
        $this->phpExecutor = $phpExecutor;
        $this->environmentManager = $environmentManager;
    }

    public function runTest(string $testName, string $phpVersion): void
    {
        $userPassword = getenv('MAGE_ADMIN_PASSWORD');
        $this->logger->info('Setting up MFTF');
        $this->phpExecutor->runCommand('php /magento/magento-ce/vendor/bin/mftf reset --hard', $phpVersion);
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
        $this->phpExecutor->runCommand('php /magento/magento-ce/vendor/bin/mftf build:project', $phpVersion);

        $this->environmentManager->startSelenium($phpVersion);

        $this->logger->info('Running MFTF test ' . $testName . ' using PHP ' . $phpVersion);
        $this->phpExecutor->runCommand('php /magento/magento-ce/vendor/bin/mftf run:test ' . $testName, $phpVersion);
    }
}
