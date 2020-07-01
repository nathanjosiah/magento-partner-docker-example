<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Config;

use Magento\UpgradeTool\ConfigInterface;

/**
 *
 */
class TestConfig
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getInstallConfig(): array
    {
        // TODO: this shouldn't return all the data. we need contracts instead
        return $this->data[ConfigInterface::PHASE_FROM];
    }

    public function getInstallType(): string
    {
        return $this->data[ConfigInterface::PHASE_FROM]['type'];
    }

    /**
     * @param string $phase One of the ConfigInterface::PHASE_* constants
     * @param string $serviceName
     * @param string $optionName
     * @return string
     */
    public function getServiceOption(string $phase, string $serviceName, string $optionName): string
    {
        return $this->data[$phase]['services'][$serviceName][$optionName];
    }

    public function getPreInstallCommands(): array
    {
        return [/* ... */];
    }
    public function getPostInstallCommands(): array
    {
        return $this->data[ConfigInterface::PHASE_FROM][ConfigInterface::EVENT_AFTER];
    }
    public function getPreUpgradeCommands(): array
    {
        return [/* ... */];
    }
    public function getPostUpgradeCommands(): array
    {
        return [/* ... */];
    }
}
