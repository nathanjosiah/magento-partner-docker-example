<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Config\ConfigReader;
use Magento\UpgradeTool\Config\TestConfig;
use Magento\UpgradeTool\Config\TestConfigFactory;

/**
 *
 */
class Config implements ConfigInterface
{
    /**
     * @var TestConfigFactory
     */
    private $testConfigFactory;
    /**
     * @var ConfigReader
     */
    private $configReader;

    public function __construct(TestConfigFactory $testConfigFactory, ConfigReader $configReader)
    {
        $this->testConfigFactory = $testConfigFactory;
        $this->configReader = $configReader;
    }

    public function getTestConfig(string $testName): TestConfig
    {
        $data = $this->configReader->read();

        return $this->testConfigFactory->create($data['tests'][$testName]);
    }
}
