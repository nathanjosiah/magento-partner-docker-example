<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;

/**
 *
 */
class ConfigCommand
{
    /**
     * @var Tool
     */
    private $toolExecutor;

    public function __construct(
        Tool $toolExecutor
        // Config $config
    ) {
        // $this->config = $config;
        $this->toolExecutor = $toolExecutor;
    }

    public function executeFromVersion(string $testName): void
    {
        // desired pseudo-implementation:

        // $testConfig = $this->config->getTestConfig($testName);
        // $stepsAndStuff = $testConfig->getFromVersionStepsOrSomething('before');


        // Fake implementation placeholder:
        $this->toolExecutor->runCommand('setup:install --php 7.3');
    }

    public function executeAfterFromVersion(string $testName): void
    {
        // Again, make more flexible as described above
        $this->toolExecutor->runCommand('verify:mftf AdminLoginTest --php 7.3');
    }

    public function executeToVersion(string $testName): void
    {
        // TODO
    }

    public function executeAfterToVersion(string $testName): void
    {
        $this->toolExecutor->runCommand('verify:mftf AdminLoginTest --php 7.4');
    }
}
