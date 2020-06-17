<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Config\Dom;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GetPhpTestVersion extends Command
{
    protected static $defaultName = 'test:config:get-php-version';

    protected function configure()
    {
        $this->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'The name of the test'
        );
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'The path to the config'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dom = new Dom();
        $document = $dom->read(file_get_contents($input->getOption('config')));
        $xpath = new \DOMXPath($document);
        $result = $xpath->query('.//*[@name="' . $input->getOption('name') . '"]/services/service[@name="php"]/arguments/argument[@name="version"]');
        $output->write($result->item(0)->nodeValue);

        return 0;
    }
}
