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

class VerifySetup extends AbstractCommand
{
    protected static $defaultName = 'setup:verify';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->log('Accessing magento');

        $return = `docker run --network cicd --rm curlimages/curl -s http://magento/magento_version/`;
        $this->log((string)$return, 'yellow');

        $this->log('Testing guest cart creation');
        $return = `docker run --network cicd --rm curlimages/curl -s -X POST http://magento/rest/V1/guest-carts`;
        $this->log((string)$return, 'yellow');

        return 0;
    }
}
