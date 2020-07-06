<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Check\Mftf;
use Magento\UpgradeTool\Environment\EnvironmentManager;
use Magento\UpgradeTool\Executor\Php;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunMftfTest extends AbstractCommand
{
    protected static $defaultName = 'verify:mftf';
    /**
     * @var Mftf
     */
    private $mftf;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Php $phpExecutor,
        LoggerInterface $logger,
        Mftf $mftf,
        string $name = null
    ) {
        parent::__construct($phpExecutor, $logger, $name);
        $this->mftf = $mftf;
        $this->logger = $logger;
    }


    protected function configure()
    {
        parent::configure();

        $this->addArgument(
            'test-name',
            InputArgument::REQUIRED,
            'The name of the test to run'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $testName = $input->getArgument('test-name');
        $phpVersion = $input->getOption('php');

        try {
            $this->mftf->runTest($testName, $phpVersion);
        } catch (\Throwable $exception) {
            $this->logger->critical($exception->getMessage());

            return 1;
        }

        return 0;
    }
}
