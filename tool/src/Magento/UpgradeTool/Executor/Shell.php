<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;


use Psr\Log\LoggerInterface;

/**
 * Executes shell commands
 */
class Shell
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute the given shell command and optionally output the result directly.
     * @param string $command
     * @param bool $return
     * @return string|null
     */
    public function exec(string $command, bool $return = true): ?string
    {
        ob_start();

        $this->logger->debug($command);

        // Will output directly without modification (compared to shell_exec)
        passthru('bash -c ' . escapeshellarg($command));

        $output = ob_get_clean();
        $this->logger->debug($output);

        if ($return) {
            return $output;
        }

        return null;
    }
}
