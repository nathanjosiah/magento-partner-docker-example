<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Laminas\Di\Config;
use Laminas\Di\Injector;
use Magento\UpgradeTool\ObjectManager;

require __DIR__ . '/vendor/autoload.php';

$config = include __DIR__ . '/etc/di.php';
$injector = new Injector(new Config($config));
$objectManager = new ObjectManager($injector);
ObjectManager::setInstance($objectManager);
