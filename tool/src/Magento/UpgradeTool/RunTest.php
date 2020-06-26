<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Executor\Php;
use Magento\UpgradeTool\Executor\Test as TestExecutor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunTest extends Command
{
    protected static $defaultName = 'test:run';
    /**
     * @var TestExecutor
     */
    private $testExecutor;

    public function __construct(
        TestExecutor $testExecutor,
        string $name = null
    ) {
        parent::__construct($name);
        $this->testExecutor = $testExecutor;
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
        $testName = $input->getArgument('test-name');
        $this->testExecutor->exec($testName);

        return 0;
    }
}
