<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Environment\EnvironmentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildEnvironment extends Command
{
    protected static $defaultName = 'env:build';
    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    /**
     * @param EnvironmentManager $environmentManager
     * @param string|null $name
     */
    public function __construct(EnvironmentManager $environmentManager, string $name = null)
    {
        parent::__construct($name);
        $this->environmentManager = $environmentManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->environmentManager->initialize();

        return 0;
    }
}
