<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Config\TestConfig;

/**
 *
 */
interface ConfigInterface
{
    const EVENT_BEFORE = 'before';
    const EVENT_AFTER = 'after';
    const PHASE_FROM = 'fromVersion';
    const PHASE_TO = 'toVersion';

    public function getTestConfig(string $testName): TestConfig;
}
