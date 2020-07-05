<?php


namespace Magento\UpgradeTool\Executor;


class ScriptExecutor
{
    const SERVICE_SELENIUM = 'selenium';
    const SERVICE_PHP = 'php';
    const SERVICE_MYSQL = 'mysql';
    const SERVICE_ELASTICSEARCH = 'elasticsearch';
    const SERVICE_NGINX = 'nginx';
    const MYSQL_VERSION_5_7 = '5.7';
    const PHP_VERSION_7_3 = '7.3';
    const PHP_VERSION_7_4 = '7.4';
    const ELASTICSEARCH_VERSION_7_6 = '7.6';
    const NGINX_PHP_7_3 = '7.3';
    const NGINX_PHP_7_4 = '7.4';

    /**
     * @var Shell
     */
    private $shellExecutor;

    /**
     * ScriptExecutor constructor.
     * @param Shell $shellExecutor
     */
    public function __construct(Shell $shellExecutor)
    {
        $this->shellExecutor = $shellExecutor;
    }

    public function startService(string $service, string $version=null): string
    {
        return $this->shellExecutor->exec('/app/scripts/start-service.sh ' . escapeshellarg($service) . ' ' . escapeshellarg($version));
    }

    public function stopService(string $service, string $version=null): string
    {
        return $this->shellExecutor->exec('/app/scripts/stop-service.sh ' . escapeshellarg($service) . ' ' . escapeshellarg($version));
    }

    public function runPhpCommand(string $command, string $version): string
    {
        return $this->shellExecutor->exec('/app/scripts/php-command.sh ' . escapeshellarg($command) . ' ' . escapeshellarg($version));
    }

    public function runToolCommand(string $command): string
    {
        return $this->shellExecutor->exec('/app/scripts/tool-command.sh ' . escapeshellarg($command));
    }
}