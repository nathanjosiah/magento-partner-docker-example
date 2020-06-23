<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use http\Exception\RuntimeException;
use Magento\UpgradeTool\Config\Dom;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class SetupInstall extends AbstractCommand
{
    protected static $defaultName = 'setup:install';

    // private $config = [];
    private $test;
    private $dom;

    private $input;

    protected function configure()
    {
        $this->setDescription('Install magento');

        $this->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'The name of the test'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->input = $input;
        $composerUsername = getenv('COMPOSER_USERNAME');
        $composerPassword = getenv('COMPOSER_PASSWORD');

        $this->dom = new Dom();
        $this->dom->read(file_get_contents($input->getOption('config')));
        $this->test = $this->dom->getTest($input->getOption('name'));
        $fromVersion = $this->dom->getFromVersion($this->test);

        $this->composerCreate($fromVersion);

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

        $arguments = $this->getArguments();
        $this->runPhp("php /magento/magento-ce/bin/magento setup:install $arguments");

        $this->runPhp('php /magento/magento-ce/bin/magento de:mo:se production');

        $this->log('Configuring magento for mftf');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf reset --hard');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf build:project');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/admin_account_sharing 1');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/use_form_key 0');

        return 0;
    }

    private function createAuth(): void
    {
        $composerUsername = getenv('COMPOSER_USERNAME');
        $composerPassword = getenv('COMPOSER_PASSWORD');

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
    }

    private function composerCreate($node): void
    {
        $this->beforeCreate($node);
        switch($node->getAttribute('type')) {
            case 'composer':
                $path = $this->dom->getPath($node);
                if (file_exists("$path/vendor")) {
                    $this->log('Magento is already installed');
                    // return;
                }
                $package = $this->dom->getPackage($node);
                $version = $this->dom->getVersion($node);
                $this->log("Installing Magento $version package ($package).");
                $this->log('Creating composer auth.json file,');
                $this->createAuth();
                $command = "composer create-project --repository-url=https://repo.magento.com/ $package:$version $path";
                // $this->runPhp($command);
                $this->log($command);
                break;
            default:
                throw new \RuntimeException('Unknown source type: ' . $node->getAttribute('type'));
                break;
        }
        $this->afterCreate($node);
    }

    private function beforeCreate($node): void
    {
        $commands = $this->dom->getBefore($node);
        $this->log((string)$commands->length);
    }

    private function afterCreate($node): void
    {
        $commands = $this->dom->getAfter($node);
        $this->log((string)$commands->length);
    }

    /**
     * Loads setting for
     */
/*
    private function loadConfig(): void
    {
        $dom = new Dom();
        $dom->read(file_get_contents($this->input->getOption('config')));
        // TODO: relative nodes
        //$node = $dom->query('.//*[@name="' . $this->input->getOption('name') . '"]/fromVersion/after/execute[@tool="bin/magento"]/arguments');

        foreach ($node->item(0)->childNodes as $item) {
            if ($item->nodeType != XML_TEXT_NODE) {
                $this->config[$item->nodeName] = $item->nodeValue;
            }
        }

    }
*/
    /**
     * Provides arguments for bin/magento setup:install command
     * @return string
     */
    /*
    private function getArguments(): string
    {
        if (empty($this->config)) {
            $this->loadConfig();
        }
        $arguments = [];
        foreach($this->config as $parameter => $value) {
            if (!$value) {
                $arguments[] = "--$parameter";
            } else {
                $arguments[] = "--$parameter=$value";
            }
        }
        return implode(' ', $arguments);
    }
*/
}

