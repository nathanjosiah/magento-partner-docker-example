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

    public function __construct(LoggerInterface $logger, Php $phpExecutor)
    {
        $this->logger = $logger;
        $this->phpExecutor = $phpExecutor;
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
        $installConfig = $config->getInstallConfig();
        $package = $installConfig['package'] . ':' . $installConfig['version'];
        $phpVersion = $config->getServiceOption(ConfigInterface::PHASE_FROM, 'php', 'version');
        $this->phpExecutor->runCommand(
            'php composer \
            create-project --repository-url=https://repo.magento.com/ \
            ' . $package . '\
             /magento/magento-ce',
            $phpVersion
        );
    }
}
