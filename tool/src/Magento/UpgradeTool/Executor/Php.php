<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;

use Psr\Log\LoggerInterface;

/**
 * Executes php commands using docker
 */
class Php
{
    /**
     * @var Shell
     */
    private $shellExecutor;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Shell $shellExecutor, LoggerInterface $logger)
    {
        $this->shellExecutor = $shellExecutor;
        $this->logger = $logger;
    }

    /**
     * Run a PHP command with the given version
     *
     * @param string $command
     * @param string $phpVersion
     * @return string|null
     */
    public function runCommand(string $command, string $phpVersion): ?string
    {
        $full = 'docker run --rm \
            --network cicd\
            -e COMPOSER_HOME=/magento/.composer\
            -v mage:/magento \
            --link magento-php' . str_replace('.', '', $phpVersion) . ':magento \
            magento/magento-cloud-docker-php:' . $phpVersion .'-cli-1.2 \
            ' . $command;

        return $this->shellExecutor->exec($full, true);
    }
}
