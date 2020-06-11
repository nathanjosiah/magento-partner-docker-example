<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\UpgradeTool\GetPhpTestVersion;
use Magento\UpgradeTool\RunUnitTests;
use Magento\UpgradeTool\SetupInstall;
use Magento\UpgradeTool\VerifySetup;
use Symfony\Component\Console\Application;
use Magento\UpgradeTool\RunTest;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->add(new GetPhpTestVersion());
$application->add(new RunTest());
$application->add(new SetupInstall());
$application->add(new VerifySetup());
$application->add(new RunUnitTests());
$application->run();
