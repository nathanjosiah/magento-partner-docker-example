<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Config;

/**
 *
 */
class FileCollector
{
    public function collect(): array
    {
        return [TMP_HACK_APP_DIR . '/etc/config.xml'];
    }
}
