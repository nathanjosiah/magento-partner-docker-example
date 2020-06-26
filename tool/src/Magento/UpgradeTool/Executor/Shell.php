<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Executor;


/**
 * Executes shell commands
 */
class Shell
{
    /**
     * Execute the given shell command and optionally output the result directly.
     * @param string $command
     * @param bool $return
     * @return string|null
     */
    public function exec(string $command, bool $return = false): ?string
    {
        if ($return) {
            //ob_start();
        }

        // Will output directly without modification (compared to shell_exec)
        passthru('bash -c ' . escapeshellarg($command));

        if ($return) {
            //return ob_get_clean();
        }

        return null;
    }
}
