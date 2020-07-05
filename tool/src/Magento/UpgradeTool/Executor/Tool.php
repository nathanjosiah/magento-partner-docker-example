<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;

/**
 * Executes php commands using docker
 */
class Tool
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
     * Run a tool command with the given version
     *
     * @param string $command
     * @return string|null
     */
    public function runCommand(string $command): ?string
    {
        return $this->scriptExecutor->runToolCommand($command);
    }
}
