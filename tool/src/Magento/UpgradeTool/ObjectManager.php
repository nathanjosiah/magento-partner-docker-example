<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Laminas\Di\InjectorInterface;
use Psr\Container\ContainerInterface;

class ObjectManager implements ContainerInterface
{
    /**
     * @var array
     */
    private $cache;

    /**
     * @var self
     */
    static $instance;

    /**
     * @var InjectorInterface
     */
    private $injector;

    /**
     * @param InjectorInterface $injector
     */
    public function __construct(InjectorInterface $injector)
    {
        $this->injector = $injector;
        $this->cache[static::class] = $this;
        $this->cache[self::class] = $this;
    }

    /**
     * Get the configured singleton instance
     *
     * @return ObjectManager
     */
    public static function getInstance(): ObjectManager
    {
        return static::$instance;
    }

    /**
     * Set the singleton instance
     *
     * @param ObjectManager $objectManager
     * @return ObjectManager
     */
    public static function setInstance(ObjectManager $objectManager): void
    {
        static::$instance = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function create($id)
    {
        return $this->injector->create($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return isset($this->cache[$id]) || $this->injector->canCreate($id);
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!isset($this->cache[$id])) {
            $this->cache[$id] = $this->injector->create($id);
        }

        return $this->cache[$id];
    }
}
