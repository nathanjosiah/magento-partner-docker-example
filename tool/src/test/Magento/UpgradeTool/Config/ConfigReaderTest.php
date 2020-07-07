<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);
namespace Magento\UpgradeTool\Config;

use Magento\UpgradeTool\ObjectManager;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /**
     * Basic check to ensure the config is parsed
     */
    public function testConfigIsParsed()
    {
        /** @var ConfigReader $reader */
        $reader = ObjectManager::getInstance()
            ->get(ConfigReader::class);
        $config = $reader->read();
        self::assertSame('composer', $config['tests']['BasicUpgradeTest']['fromVersion']['type']);
    }
}
