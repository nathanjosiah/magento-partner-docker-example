<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Environment\EnvironmentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildEnvironment extends Command
{
    protected static $defaultName = 'env:build';
    /**
     * @var EnvironmentManager
     */
    private $environmentManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EnvironmentManager $environmentManager
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(
        EnvironmentManager $environmentManager,
        LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
        $this->environmentManager = $environmentManager;
        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->environmentManager->initialize();
        } catch(\Throwable $exception) {
            $this->logger->critical($exception->getMessage());
            return 1;
        }

        return 0;
    }
}
