<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Executor\ScriptExecutor;

/**
 *
 */
class ArtifactManager
{
    private $prefix = '';

    /**
     * @var ScriptExecutor
     */
    private $scriptExecutor;

    public function __construct(ScriptExecutor $scriptExecutor)
    {
        $this->scriptExecutor = $scriptExecutor;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function saveFolderAsArtifact(string $artifactPath, string $artifactName): void
    {
        $this->scriptExecutor->saveFolderAsArtifact($artifactPath, ($this->prefix ? $this->prefix . '_' : '') . $artifactName);
    }
}
