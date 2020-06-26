<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;

use Psr\Log\LoggerInterface;

/**
 *
 */
class Test
{
    /**
     * @var Tool
     */
    private $toolExecutor;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ConfigCommand
     */
    private $configCommandExecutor;

    public function __construct(
        Tool $toolExecutor,
        LoggerInterface $logger,
        ConfigCommand $configCommandExecutor
    ) {
        $this->toolExecutor = $toolExecutor;
        $this->logger = $logger;
        $this->configCommandExecutor = $configCommandExecutor;
    }

    public function exec(string $testName): void
    {
        $this->logger->info('Starting Test ' . $testName);

        //$this->logger->info('Executing pre-installation steps');
        //$this->configCommandExecutor->executeBeforeFromVersion($testName);

        $this->logger->info('Installing Magento');
        $this->configCommandExecutor->executeFromVersion($testName);

        $this->logger->info('Post-installation steps');
        $this->configCommandExecutor->executeAfterFromVersion($testName);

        //$this->logger->info('Pre-upgrade steps');
        //$this->configCommandExecutor->executeBeforeToVersion($testName);

        $this->logger->info('Upgrading Magento');
        $this->configCommandExecutor->executeToVersion($testName);

        $this->logger->info('Post-Upgrade Steps');
        $this->configCommandExecutor->executeAfterToVersion($testName);

        $this->logger->info('Ending Test ' . $testName);
    }
}
