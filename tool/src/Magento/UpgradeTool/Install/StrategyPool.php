<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Install;

/**
 *
 */
class StrategyPool
{
    /**
     * @var array
     */
    private $pool;

    /**
     * @param array $pool
     */
    public function __construct(array $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @param string $name
     * @return StrategyInterface
     */
    public function getStrategy(string $name): StrategyInterface
    {
        return $this->pool[$name];
    }
}
