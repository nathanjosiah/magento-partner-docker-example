<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\ObjectManager;

use Laminas\Di\Resolver\ValueInjection;
use Magento\UpgradeTool\ObjectManager;
use Psr\Container\ContainerInterface;

/**
 *
 */
class ObjectArrayResolver extends ValueInjection
{
    public function toValue(ContainerInterface $container)
    {
        $items = [];
        $objectManager = ObjectManager::getInstance();
        foreach ($this->value as $object) {
            $items[] = $objectManager->get($object);
        }

        return $items;
    }

    public function isExportable(): bool
    {
        return false;
    }
}
