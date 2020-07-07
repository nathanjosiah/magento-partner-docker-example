<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Check\Integration;
use Magento\UpgradeTool\Executor\Php;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunIntegrationTest extends AbstractCommand
{
    protected static $defaultName = 'verify:integration';
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Integration
     */
    private $integration;

    /**
     * @param Php $phpExecutor
     * @param LoggerInterface $logger
     * @param Integration $integration
     * @param string|null $name
     */
    public function __construct(
        Php $phpExecutor,
        LoggerInterface $logger,
        Integration $integration,
        string $name = null
    ) {
        parent::__construct($phpExecutor, $logger, $name);
        $this->logger = $logger;
        $this->integration = $integration;
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
            $this->integration->runTest($testName, $phpVersion);
        } catch (\Throwable $exception) {
            $this->logger->critical($exception->getMessage());

            return 1;
        }

        return 0;
    }
}
