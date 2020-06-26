<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Environment;

use Magento\UpgradeTool\Executor\Shell;
use Psr\Log\LoggerInterface;

/**
 * Initialize the docker environment
 */
class EnvironmentManager
{
    /**
     * @var Shell
     */
    private $shellExecutor;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Shell $shellExecutor
     * @param LoggerInterface $logger
     */
    public function __construct(Shell $shellExecutor, LoggerInterface $logger)
    {
        $this->shellExecutor = $shellExecutor;
        $this->logger = $logger;
    }

    public function initialize(): void
    {
        $this->logger->info('Initializing environment');
        $this->logger->info('Pulling PHP Images');
        $this->shellExecutor->exec('docker pull magento/magento-cloud-docker-php:7.3-cli-1.2');
        $this->shellExecutor->exec('docker pull magento/magento-cloud-docker-php:7.3-fpm-1.2');
        $this->shellExecutor->exec('docker pull magento/magento-cloud-docker-php:7.4-cli-1.2');
        $this->shellExecutor->exec('docker pull magento/magento-cloud-docker-php:7.4-fpm-1.2');

        $this->logger->info('Creating volume');
        $this->shellExecutor->exec('docker volume create --name mage');
        $this->logger->info('Creating network');
        $this->shellExecutor->exec('docker network create --driver bridge cicd');

        $this->logger->info('Starting PHP-FPM 7.3');
        $this->shellExecutor->exec('
            docker run --rm -d \
            --name fpm-73 \
            --network cicd \
            -v mage:/magento\
            -w=/magento/magento-ce\
            magento/magento-cloud-docker-php:7.3-fpm-1.2
        ');

        $this->logger->info('Starting PHP-FPM 7.4');
        $this->shellExecutor->exec('
            docker run --rm -d \
            --name fpm-74 \
            --network cicd \
            -v mage:/magento\
            -w=/magento/magento-ce\
            magento/magento-cloud-docker-php:7.4-fpm-1.2
        ');

        $this->logger->info('Starting MariaDB');
        $this->shellExecutor->exec('
            docker run --rm -d \
            --name db \
            --network cicd \
            -e MYSQL_ROOT_PASSWORD=secretpw \
            -e \'MYSQL_ROOT_HOST=%\' \
            -e MYSQL_DATABASE=main \
            db
        ');

        $this->logger->info('Starting ElasticSearch 7');
        $this->shellExecutor->exec('
            docker run --rm -d \
             --name elasticsearch7 \
             --network cicd \
             -e "discovery.type=single-node" \
            magento/magento-cloud-docker-elasticsearch:7.6-1.2
        ');

        $this->logger->info('Starting Nginx for php73');
        $this->shellExecutor->exec('
            docker run --rm -d \
            --network cicd \
            --name magento-php73 \
            -e FPM_HOST=fpm-73\
            -v mage:/magento magento
        ');
        $this->logger->info('Starting Nginx for php74');
        $this->shellExecutor->exec('
            docker run --rm -d \
            --network cicd \
            --name magento-php74 \
            -e FPM_HOST=fpm-74\
            -v mage:/magento magento
        ');
        $this->logger->info('Starting Selenium');

        $this->shellExecutor->exec('
            docker run --rm -d \
            --name selenium \
            --network cicd \
            -v /dev/shm:/dev/shm \
            selenium/standalone-chrome-debug:3.141.59-gold
        ');
    }
}
