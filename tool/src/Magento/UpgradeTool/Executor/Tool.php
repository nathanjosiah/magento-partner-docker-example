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
     * @var Shell
     */
    private $shellExecutor;

    public function __construct(Shell $shellExecutor)
    {
        $this->shellExecutor = $shellExecutor;
    }

    /**
     * Run a tool command with the given version
     *
     * @param string $command
     * @return string|null
     */
    public function runCommand(string $command): ?string
    {
        $full = 'docker run --rm \
         --network cicd \
         --env-file <(env | grep MAGE_) \
         -v /var/run/docker.sock:/var/run/docker.sock \
         -v mage:/magento \
         tool ' . $command;

        return $this->shellExecutor->exec($full, false);
    }
}
