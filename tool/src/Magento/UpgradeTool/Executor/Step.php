<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;

use Magento\UpgradeTool\ArtifactManager;
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
    /**
     * @var ArtifactManager
     */
    private $artifactManager;

    /**
     * @param Tool $toolExecutor
     * @param ConfigInterface $config
     * @param StrategyPool $installStrategyPool
     * @param ConfigCommand $commandExecutor
     * @param ArtifactManager $artifactManager
     */
    public function __construct(
        Tool $toolExecutor,
        ConfigInterface $config,
        StrategyPool $installStrategyPool,
        ConfigCommand $commandExecutor,
        ArtifactManager $artifactManager
    ) {
        $this->toolExecutor = $toolExecutor;
        $this->config = $config;
        $this->installStrategyPool = $installStrategyPool;
        $this->commandExecutor = $commandExecutor;
        $this->artifactManager = $artifactManager;
    }

    public function executeFromVersion(string $testName): void
    {
        $this->artifactManager->setPrefix($testName . '_' . ConfigInterface::PHASE_FROM);
        $testConfig = $this->config->getTestConfig($testName);
        $strategy = $this->installStrategyPool->getStrategy($testConfig->getInstallType());
        $strategy->install($testConfig);
    }

    public function executeAfterFromVersion(string $testName): void
    {
        $testConfig = $this->config->getTestConfig($testName);
        foreach ($testConfig->getPostInstallCommands() as $key => $command) {
            $this->artifactManager->setPrefix(
                $testName . '_' .
                ConfigInterface::PHASE_FROM . '_' .
                ConfigInterface::EVENT_AFTER . '_' .
                $key
            );
            $this->commandExecutor->runCommand($command);
        }
    }

    public function executeToVersion(string $testName): void
    {
        $this->artifactManager->setPrefix($testName . '_' . ConfigInterface::PHASE_TO);
    }

    public function executeAfterToVersion(string $testName): void
    {
        $testConfig = $this->config->getTestConfig($testName);
        foreach ($testConfig->getPostUpgradeCommands() as $key => $command) {
            $this->artifactManager->setPrefix(
                $testName . '_' .
                ConfigInterface::PHASE_TO . '_' .
                ConfigInterface::EVENT_AFTER . '_' .
                $key
            );
            $this->commandExecutor->runCommand($command);
        }
    }
}
