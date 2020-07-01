<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;

use Magento\UpgradeTool\ConfigInterface;
use Magento\UpgradeTool\Install\StrategyPool;

/**
 *
 */
class Step
{
    /**
     * @var Tool
     */
    private $toolExecutor;
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var StrategyPool
     */
    private $installStrategyPool;
    /**
     * @var ConfigCommand
     */
    private $commandExecutor;

    public function __construct(
        Tool $toolExecutor,
        ConfigInterface $config,
        StrategyPool $installStrategyPool,
        ConfigCommand $commandExecutor
    ) {
        $this->toolExecutor = $toolExecutor;
        $this->config = $config;
        $this->installStrategyPool = $installStrategyPool;
        $this->commandExecutor = $commandExecutor;
    }

    public function executeFromVersion(string $testName): void
    {
        $testConfig = $this->config->getTestConfig($testName);
        $strategy = $this->installStrategyPool->getStrategy($testConfig->getInstallType());
        $strategy->install($testConfig);
    }

    public function executeAfterFromVersion(string $testName): void
    {
        $testConfig = $this->config->getTestConfig($testName);
        foreach ($testConfig->getPostInstallCommands() as $command) {
            $this->commandExecutor->runCommand($command);
        }
    }

    public function executeToVersion(string $testName): void
    {
        $testConfig = $this->config->getTestConfig($testName);
        foreach ($testConfig->getPreUpgradeCommands() as $command) {
            $this->commandExecutor->runCommand($command);
        }
    }

    public function executeAfterToVersion(string $testName): void
    {
        $testConfig = $this->config->getTestConfig($testName);
        foreach ($testConfig->getPostUpgradeCommands() as $command) {
            $this->commandExecutor->runCommand($command);
        }
    }
}
