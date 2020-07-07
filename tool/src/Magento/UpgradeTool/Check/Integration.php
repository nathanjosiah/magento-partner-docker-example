<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Check;

use Magento\UpgradeTool\ArtifactManager;
use Magento\UpgradeTool\Environment\EnvironmentManager;
use Magento\UpgradeTool\Executor\Php;
use Magento\UpgradeTool\Executor\ScriptExecutor;
use Psr\Log\LoggerInterface;

/**
 *
 */
class Integration
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
     * @var EnvironmentManager
     */
    private $environmentManager;
    /**
     * @var ScriptExecutor
     */
    private $scriptExecutor;
    /**
     * @var ArtifactManager
     */
    private $artifactManager;

    /**
     * @param LoggerInterface $logger
     * @param Php $phpExecutor
     * @param ScriptExecutor $scriptExecutor
     * @param EnvironmentManager $environmentManager
     * @param ArtifactManager $artifactManager
     */
    public function __construct(
        LoggerInterface $logger,
        Php $phpExecutor,
        ScriptExecutor $scriptExecutor,
        EnvironmentManager $environmentManager,
        ArtifactManager $artifactManager
    ) {
        $this->logger = $logger;
        $this->phpExecutor = $phpExecutor;
        $this->environmentManager = $environmentManager;
        $this->scriptExecutor = $scriptExecutor;
        $this->artifactManager = $artifactManager;
    }

    public function runTest(string $testName, string $phpVersion): void
    {
        $this->logger->info('Setting up MFTF');
        file_put_contents('/magento/magento-ce/dev/tests/integration/etc/install-config-mysql.php', <<<ENV
<?php
return [
    'db-host' => 'db',
    'db-user' => 'root',
    'db-password' => 'secretpw',
    'db-name' => 'magento_integration_tests',
    'db-prefix' => '',
    'backend-frontname' => 'backend',
    'admin-user' => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
    'admin-password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
    'admin-email' => \Magento\TestFramework\Bootstrap::ADMIN_EMAIL,
    'admin-firstname' => \Magento\TestFramework\Bootstrap::ADMIN_FIRSTNAME,
    'admin-lastname' => \Magento\TestFramework\Bootstrap::ADMIN_LASTNAME,
];
ENV
        );
        try {
            $this->scriptExecutor->runSql('CREATE DATABASE magento_integration_tests');
        } catch (\Throwable $exception) {
            $this->logger->info('Create database failed. Probably already exists.');
        }
        $this->logger->info('Running Integration test ' . $testName . ' using PHP ' . $phpVersion);

        try {
            $this->phpExecutor->runCommand('php /magento/magento-ce/vendor/bin/phpunit -c /magento/magento-ce/dev/tests/integration/phpunit.xml.dist ' . $testName, $phpVersion);
        } catch (\Throwable $exception) {
            $this->artifactManager->saveFolderAsArtifact('/magento/magento-ce/dev/tests/integration/var/allure-results', $testName);
        }
    }
}
