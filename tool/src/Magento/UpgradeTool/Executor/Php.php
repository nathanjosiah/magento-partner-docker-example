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
     * @var ScriptExecutor
     */
    private $scriptExecutor;

    public function __construct(ScriptExecutor $scriptExecutor)
    {
        $this->scriptExecutor = $scriptExecutor;
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
        return $this->scriptExecutor->runPhpCommand($command, $phpVersion);
    }
}
