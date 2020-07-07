<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Executor\Php;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class AbstractCommand extends Command
{
    /**
     * @var Php
     */
    private $phpExecutor;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Php $phpExecutor
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(Php $phpExecutor, LoggerInterface $logger, string $name = null)
    {
        parent::__construct($name);
        $this->phpExecutor = $phpExecutor;
        $this->logger = $logger;
    }

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
            InputOption::VALUE_REQUIRED,
            'Which PHP version to use'
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
        $this->phpVersion = $input->getOption('php');
        $this->output = $output;
    }

    protected function log(string $message): void
    {
        $this->logger->info($message);
    }

    protected function runPhp(string $command, string $phpVersion = null, string $path = null): void
    {
        // Change default directory to make sure that both absolute and relative commands work
        if ($path) {
            chdir($path);
        }
        $this->phpExecutor->runCommand($command, $phpVersion ?: $this->phpVersion);
    }
}
