<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Install;

use Magento\UpgradeTool\Config\TestConfig;

/**
 *
 */
interface StrategyInterface
{
    public function install(TestConfig $config): void;
}
