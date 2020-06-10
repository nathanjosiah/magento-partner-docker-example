<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunUnitTests extends Command
{
    protected static $defaultName = 'self:run-tests';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        passthru(\PHP_BINARY . ' /app/vendor/bin/phpunit -c /app/test/phpunit.xml');

        return 0;
    }
}
