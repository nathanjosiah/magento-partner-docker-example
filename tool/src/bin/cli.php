<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\UpgradeTool\ApplicationFactory;
use Magento\UpgradeTool\ObjectManager;

require_once __DIR__ . '/../bootstrap.php';

$application = ObjectManager::getInstance()
    ->get(ApplicationFactory::class)
    ->create();
$application->run();
