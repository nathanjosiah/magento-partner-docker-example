<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Symfony\Component\Console\Application;
use Magento\UpgradeTool\MainCommand;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->add(new MainCommand());
$application->run();
