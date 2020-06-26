<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Magento\UpgradeTool\Config\Dom;
use Magento\UpgradeTool\Config\CommandFlow;
use Magento\UpgradeTool\ObjectManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class SetupInstall extends AbstractCommand
{
    protected static $defaultName = 'setup:install';

    private $test;
    private $dom;
    private string $path;

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
        // We probably need to move config reading much earlier
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
        $this->path = $this->dom->getPath($node);
        $flow = new CommandFlow($this->dom, $node);
        // Before commands
        $this->log('Before command flow');
        $this->commandFlow($flow->getBefore());
        switch($node->getAttribute('type')) {
            case 'composer':
                if (file_exists("{$this->path}/vendor")) {
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
                // This probably belongs to after flow instead of being hardcoded
                $this->log('Running highly unsafe permission change to 777 for everything for now.');
                `chmod -R 777 {$this->path}`;
                break;
            default:
                throw new \RuntimeException('Unknown source type: ' . $node->getAttribute('type'));
                break;
        }
        $this->log('After command flow');
        $this->commandFlow($flow->getAfter());
    }

    /**
     * Command to run
     * @param \DOMNodeList $commands
     */
    private function commandFlow(array $commands): void
    {
        foreach($commands as $command) {
            $this->log("Executing command {$command->getName()} (container: {$command->getContainer()}).");
            $execute = $command->buildCommand();
            $this->log($execute);
            $this->runPhp($execute, null, $this->path);
        }
    }
}
