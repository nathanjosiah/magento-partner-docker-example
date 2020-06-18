<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Config\Dom;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupInstall extends AbstractCommand
{
    protected static $defaultName = 'setup:install';

    private $config = [];

    private $input;

    protected function configure()
    {
        $this->setDescription('Install magento');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->input = $input;
        $composerUsername = getenv('COMPOSER_USERNAME');
        $composerPassword = getenv('COMPOSER_PASSWORD');

        if (file_exists('/magento/magento-ce/vendor')) {
            $this->log('Magento is already installed');
            return 0;
        }

        // TODO: Hardcoded version number
        $this->log('Installing Magento 2.3.5 package.');
        mkdir('/magento/.composer');

        file_put_contents('/magento/.composer/auth.json',
            <<<COMPOSER
{
  "http-basic": {
  "repo.magento.com": {
      "username": "${composerUsername}",
          "password": "${composerPassword}"
      }
  }
}
COMPOSER
        );
        // TODO: Use configuration options
        $this->runPhp('composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition:2.3.5 /magento/magento-ce');

        // TODO: We need to deal with this sooner rather than later
        $this->log('Running highly unsafe permission change to 777 for everything for now.');
        `chmod -R 777 /magento/magento-ce`;

        $this->log('Installing Magento');

        $parameters = $this->getParameters();
        $this->runPhp("php /magento/magento-ce/bin/magento setup:install $parameters");

        $this->runPhp('php /magento/magento-ce/bin/magento de:mo:se production');

        $this->log('Configuring magento for mftf');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf reset --hard');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf build:project');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/admin_account_sharing 1');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/use_form_key 0');

        return 0;
    }

    /*
     * TODO: This stuff should probably end up in a separate object that is either injected or passed to this one
     */
    private function loadConfig(): void
    {
        $dom = new Dom();
        $document = $dom->read(file_get_contents($this->input->getOption('config')));
        // TODO: use xpath queries instead
        $nodes = $document->getElementsByTagName('install')->item(0)->childNodes;
        foreach ($nodes as $item) {
            if ($item->nodeType != XML_TEXT_NODE) {
                $this->config[$item->nodeName] = $item->nodeValue;
            }
        }
        /*
         * TODO: deal with validation
         * I'm going to leave it here for now as an example. Maybe make validation a separate CLI command?
         * Either way we can skip validation for initial phases.
        if ($document->schemaValidate('config.xsd')) {
            echo "Validation OK\n";
        } else {
            echo "Validation failed\n";
            exit(1);
        }
        */
    }

    /**
     * Provides parameter string for bin/magento setup:install command
     * @return string
     */
    private function getParameters(): string
    {
        if (empty($this->config)) {
            $this->loadConfig();
        }
        $parameters = [];
        foreach($this->config as $parameter => $value) {
            if (!$value) {
                $parameters[] = "--$parameter";
            } else {
                $parameters[] = "--$parameter=$value";
            }
        }
        return implode(' ', $parameters);
    }
}

