<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Environment;

use Magento\UpgradeTool\Executor\ScriptExecutor;
use Magento\UpgradeTool\Executor\Shell;
use Psr\Log\LoggerInterface;

/**
 * Initialize the docker environment
 */
class EnvironmentManager
{
    /**
     * @var Shell
     */
    private $shellExecutor;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ScriptExecutor
     */
    private $scriptExecutor;

    /**
     * @param Shell $shellExecutor
     * @param LoggerInterface $logger
     * @param ScriptExecutor $scriptExecutor
     */
    public function __construct(Shell $shellExecutor, LoggerInterface $logger, ScriptExecutor $scriptExecutor)
    {
        $this->shellExecutor = $shellExecutor;
        $this->logger = $logger;
        $this->scriptExecutor = $scriptExecutor;
    }

    public function initialize(): void
    {
        $this->logger->info('Initializing environment');
        $this->logger->info('Starting PHP-FPM 7.3');
        $this->scriptExecutor->startService(ScriptExecutor::SERVICE_PHP, ScriptExecutor::PHP_VERSION_7_3);
        $this->logger->info('Starting PHP-FPM 7.4');
        $this->scriptExecutor->startService(ScriptExecutor::SERVICE_PHP, ScriptExecutor::PHP_VERSION_7_4);
        $this->logger->info('Starting MariaDB');
        $this->scriptExecutor->startService(ScriptExecutor::SERVICE_MYSQL, ScriptExecutor::MYSQL_VERSION_5_7);

        $this->logger->info('Starting ElasticSearch 7');
        $this->scriptExecutor->startService(ScriptExecutor::SERVICE_ELASTICSEARCH, ScriptExecutor::ELASTICSEARCH_VERSION_7_2);

        $this->logger->info('Starting Nginx for php73');
        $this->scriptExecutor->startService(ScriptExecutor::SERVICE_NGINX, ScriptExecutor::NGINX_PHP_7_3);
        $this->logger->info('Starting Nginx for php74');
        $this->scriptExecutor->startService(ScriptExecutor::SERVICE_NGINX, ScriptExecutor::NGINX_PHP_7_4);
    }

    public function startSelenium(string $phpVersion): void
    {
        $this->scriptExecutor->stopService(ScriptExecutor::SERVICE_SELENIUM, $phpVersion);
        $this->scriptExecutor->startService(ScriptExecutor::SERVICE_SELENIUM, $phpVersion);
    }
}
