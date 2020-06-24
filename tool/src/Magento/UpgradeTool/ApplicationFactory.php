<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Symfony\Component\Console\Application;

/**
 *
 */
class ApplicationFactory
{
    /**
     * @var array
     */
    private $commands;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(ObjectManager $objectManager, array $commands)
    {
        $this->objectManager = $objectManager;
        $this->commands = $commands;
    }

    public function create(): Application
    {
        $application = $this->objectManager->create(Application::class);
        $application->addCommands($this->commands);

        return $application;
    }
}
