<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Install\Strategy;

use Magento\UpgradeTool\Config\TestConfig;
use Magento\UpgradeTool\ConfigInterface;
use Magento\UpgradeTool\Executor\Php;
use Magento\UpgradeTool\Executor\Shell;
use Magento\UpgradeTool\Install\StrategyInterface;
use Psr\Log\LoggerInterface;

/**
 *
 */
class Git implements StrategyInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Shell
     */
    private $shellExecutor;

    public function __construct(LoggerInterface $logger, Shell $shellExecutor)
    {
        $this->logger = $logger;
        $this->shellExecutor = $shellExecutor;
    }

    /**
     * @inheritDoc
     */
    public function install(TestConfig $config): void
    {
        $this->logger->info('Installing magento using git strategy');
        $installConfig = $config->getInstallConfig();
        $command = 'git clone --depth 1 --branch ' . $installConfig['branch'] . ' ' . $installConfig['repo'];
        $this->shellExecutor->exec($command);
    }
}
