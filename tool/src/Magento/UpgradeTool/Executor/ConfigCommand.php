<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;

/**
 *
 */
class ConfigCommand
{
    /**
     * @var Shell
     */
    private $shellExecutor;
    /**
     * @var Php
     */
    private $phpExecutor;
    /**
     * @var Tool
     */
    private $toolExecutor;

    public function __construct(Shell $shellExecutor, Php $phpExecutor, Tool $toolExecutor)
    {
        $this->shellExecutor = $shellExecutor;
        $this->phpExecutor = $phpExecutor;
        $this->toolExecutor = $toolExecutor;
    }

    public function runCommand(array $command): void
    {
        //TODO resolve command with glue and such and run it
        if ($command['type'] === 'tool') {
            $this->toolExecutor->runCommand(implode(' ', $command['arguments']));
        } elseif ($command['type'] === 'php') {
            // $this->phpExecutor->runCommand(...);
        } elseif($command['type'] === 'shell') {
            // $this->shellExecutor->exec(...);
        }
    }
}
