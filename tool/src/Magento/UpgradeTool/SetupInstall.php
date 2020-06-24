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
use Symfony\Component\Console\Input\InputOption;

class SetupInstall extends AbstractCommand
{
    protected static $defaultName = 'setup:install';

    const GLUE = ' ';

    private $test;
    private $dom;

    private $input;

    /**
     * SetupInstall constructor.
     * @param Dom $dom
     */
    public function __construct(Dom $dom)
    {
        parent::__construct();
        $this->dom = $dom;
    }

    /**
     * Command configuration
     */
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->input = $input;

        // Load configuration
        $this->dom->read(file_get_contents($input->getOption('config')));
        $this->test = $this->dom->getTest($input->getOption('name'));
        $fromVersion = $this->dom->getFromVersion($this->test);

        // Install Magento
        $this->composerCreate($fromVersion);

        return 0;
    }

    /**
     * Create auth.json for composer
     */
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

    /**
     * Run composer create-project and related commands
     * @param \DOMNode $node
     */
    private function composerCreate(\DOMNode $node): void
    {
        $this->beforeCreate($node);
        switch($node->getAttribute('type')) {
            case 'composer':
                $path = $this->dom->getPath($node);
                if (file_exists("$path/vendor")) {
                    $this->log('Magento is already installed');
                    return;
                }
                $package = $this->dom->getPackage($node);
                $version = $this->dom->getVersion($node);
                $this->log("Installing Magento $version package ($package).");
                $this->log('Creating composer auth.json file,');
                $this->createAuth();
                $command = "composer create-project --repository-url=https://repo.magento.com/ $package:$version $path";
                $this->runPhp($command);
                $this->log($command);
                $this->log('Running highly unsafe permission change to 777 for everything for now.');
                `chmod -R 777 /magento/magento-ce`;
                break;
            default:
                throw new \RuntimeException('Unknown source type: ' . $node->getAttribute('type'));
                break;
        }
        $this->afterCreate($node);
    }

    /**
     * Commands to run before composer create-projects
     * @param \DOMNode $node
     */
    private function beforeCreate(\DOMNode $node): void
    {
        $commands = $this->dom->getBefore($node);
        $this->log('Before command flow.');
        $this->commandFlow($commands);
    }

    /**
     * Commands to run after composer create-projects
     * @param \DOMNode $node
     */
    private function afterCreate(\DOMNode $node): void
    {
        $commands = $this->dom->getAfter($node);
        $this->log('After command flow');
        $this->commandFlow($commands);
    }

    /**
     * Command to run
     * @param \DOMNodeList $commands
     */
    private function commandFlow(\DOMNodeList $commands): void
    {
        foreach($commands as $command) {
            $container = $command->getAttribute('container');
            $path = $command->getAttribute('path');
            $name = $command->getAttribute('name');
            $this->log("Executing command $name (container: $container).");
            $arguments = $this->dom->getArguments($command);
            $parameters = $this->buildParameters($arguments);
            $execute = "php /magento/magento-ce/$path $parameters";
            $this->log($execute);
            $this->runPhp($execute);
        }
    }

    /**
     * Build a parameter list from command arguments
     * @param array $arguments
     * @return string
     */
    private function buildParameters(array $arguments): string
    {
        $parameters = [];
        foreach($arguments as $argument) {
            $glue = self::GLUE;
            if ($argument['glue']) {
                $glue = $argument['glue'];
            }
            if ($argument['name']) {
                $parameters[] = "{$argument['name']}$glue{$argument['value']}";
            } else {
                $parameters[] = $argument['value'];
            }
        }
        return implode(' ', $parameters);
    }
}
