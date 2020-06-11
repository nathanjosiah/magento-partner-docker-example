<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class AbstractCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

    private $phpVersion;

    protected function configure()
    {
        $this->addOption(
            'php',
            null,
            InputArgument::REQUIRED,
            'Which PHP version to use'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->phpVersion = $input->getOption('php');
        $this->output = $output;
    }

    protected function log(string $string, string $color = 'blue', bool $newline = true): void
    {
        $this->output->writeln('<fg=' . $color . '>' . $string . '</>', OutputInterface::VERBOSITY_NORMAL);
    }

    protected function runPhp(string $command, string $phpVersion = null): void
    {
        $full = 'docker run --rm \
            --network cicd\
            -e COMPOSER_HOME=/magento/.composer\
            -v mage:/magento php-' . str_replace('.', '', ($phpVersion ?: $this->phpVersion)) .'-cli \
            ' . $command;
        $this->log('Running:' . $full, 'white');
        // passthru will stream the output in real time without modification
        passthru($full);
    }
}
