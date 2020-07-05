<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Install\Strategy;


use Magento\UpgradeTool\Config\TestConfig;
use Magento\UpgradeTool\ConfigInterface;
use Magento\UpgradeTool\Executor\Php;
use Magento\UpgradeTool\Executor\Shell;
use Magento\UpgradeTool\Install\StrategyInterface;
use Psr\Log\LoggerInterface;

/**
 *
 */
class Composer implements StrategyInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Php
     */
    private $phpExecutor;
    /**
     * @var Shell
     */
    private $shelExecutor;

    public function __construct(LoggerInterface $logger, Php $phpExecutor, Shell $shelExecutor)
    {
        $this->logger = $logger;
        $this->phpExecutor = $phpExecutor;
        $this->shelExecutor = $shelExecutor;
    }

    /**
     * @inheritDoc
     */
    public function install(TestConfig $config): void
    {
        $this->logger->info('Installing magento using composer strategy');
        if (file_exists('/magento/magento-ce/vendor')) {
            $this->logger->warning('Magento is already installed');
            return;
        }
        $composerUsername = getenv('MAGE_COMPOSER_USERNAME');
        $composerPassword = getenv('MAGE_COMPOSER_PASSWORD');
        $installConfig = $config->getInstallConfig();
        $package = $installConfig['package'] . ':' . $installConfig['version'];
        $phpVersion = $config->getServiceOption(ConfigInterface::PHASE_FROM, 'php', 'version');

        $this->logger->info('Installing ' . $phpVersion);
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

        $this->phpExecutor->runCommand(
            'composer create-project --repository-url=https://repo.magento.com/ ' . $package . '/magento/magento-ce',
            $phpVersion
        );

        $this->shelExecutor->exec('chmod -R 777 /magento/magento-ce');
    }
}
