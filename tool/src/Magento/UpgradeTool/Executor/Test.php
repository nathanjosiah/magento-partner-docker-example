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
     * @var Step
     */
    private $stepExecutor;

    public function __construct(
        Tool $toolExecutor,
        LoggerInterface $logger,
        Step $configCommandExecutor
    ) {
        $this->toolExecutor = $toolExecutor;
        $this->logger = $logger;
        $this->stepExecutor = $configCommandExecutor;
    }

    public function exec(string $testName): void
    {
        $this->logger->info('Starting Test ' . $testName);

        //$this->logger->info('Executing pre-installation steps');
        //$this->stepExecutor->executeBeforeFromVersion($testName);

        $this->logger->info('Installing Magento');
        $this->stepExecutor->executeFromVersion($testName);

        $this->logger->info('Post-installation steps');
        $this->stepExecutor->executeAfterFromVersion($testName);

        //$this->logger->info('Pre-upgrade steps');
        //$this->stepExecutor->executeBeforeToVersion($testName);

        $this->logger->info('Upgrading Magento');
        $this->stepExecutor->executeToVersion($testName);

        $this->logger->info('Post-Upgrade Steps');
        $this->stepExecutor->executeAfterToVersion($testName);

        $this->logger->info('Ending Test ' . $testName);
    }
}
