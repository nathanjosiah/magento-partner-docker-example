<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\UpgradeTool\ApplicationFactory;
use Magento\UpgradeTool\ObjectManager;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/../bootstrap.php';

$objectManager = ObjectManager::getInstance();
$input = new ArgvInput();
$output = new ConsoleOutput();
$output->setVerbosity(99999999);
$objectManager->set(InputInterface::class, $input);
$objectManager->set(OutputInterface::class, $output);

$application = $objectManager->get(ApplicationFactory::class)
    ->create();
$application->run($input, $output);
