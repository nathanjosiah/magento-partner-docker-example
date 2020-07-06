<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Config;

use Psr\Log\LoggerInterface;

/**
 *
 */
class Converter
{
    const CONFIG_GLUE = ' ';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var \DOMXPath
     */
    private \DOMXPath $xpath;
    /**
     * @var string
     */
    private string $path;

    /**
     * Converter constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \DOMDocument $document
     * @return array
     */
    public function convert(\DOMDocument $document): array
    {
        $this->logger->debug('Converting config');
        $this->xpath = new \DOMXPath($document);
        $config = [];
        $config['tests'] = $this->getTests($document);
        return $config;
    }

    /**
     * @param \DOMDocument $document
     * @return array
     */
    private function getTests(\DOMDocument $document): array
    {
        $testList = [];
        $tests = $this->xpath->query("//config/tests/test");
        foreach ($tests as $test) {
            $testList[$test->getAttribute('name')] = [
                'fromVersion' => $this->getFromVersion($test),
                'toVersion' => $this->getToVersion($test)
            ];
        }
        return $testList;
    }

    /**
     * @param \DOMNode $node
     * @return array|array[]
     */
    private function getFromVersion(\DOMNode $node): array
    {
        $fromVersionList = [];
        $fromVersion = $this->xpath->query('.//fromVersion', $node)[0];
        $fromVersionList['type'] = $fromVersion->getAttribute('type');

        $fromVersionList = array_merge($fromVersionList, $this->getTypeNodes($fromVersion));

        $this->path = $this->xpath->query('.//path', $fromVersion)[0]->nodeValue;
        $fromVersionList['path'] = $this->path;

        $fromVersionList['services'] = $this->getServices($fromVersion);
        $fromVersionList['before'] = $this->getCommands($fromVersion, 'before');
        $fromVersionList['after'] = $this->getCommands($fromVersion, 'after');

        return $fromVersionList;
    }

    /**
     * @param \DOMNode $node
     * @return array
     */
    private function getToVersion(\DOMNode $node): array
    {
        $toVersionList = [];
        $toVersion = $this->xpath->query('.//toVersion', $node)[0];
        $toVersionList['type'] = $toVersion->getAttribute('type');

        $toVersionList = array_merge($toVersionList, $this->getTypeNodes($toVersion));

        $toVersionList['services'] = $this->getServices($toVersion);
        $toVersionList['before'] = $this->getCommands($toVersion, 'before');
        $toVersionList['after'] = $this->getCommands($toVersion, 'after');

        return $toVersionList;
    }

    /**
     * @param \DOMNode $node
     * @return array
     */
    private function getTypeNodes(\DOMNode $node): array
    {
        $typeList = [];
        switch ($node->getAttribute('type')) {
            case 'composer':
                $typeList['package'] = $this->xpath->query('.//package', $node)[0]->nodeValue;
                $typeList['version'] = $this->xpath->query('.//version', $node)[0]->nodeValue;
                break;
            case 'git':
                $typeList['repo'] = $this->xpath->query('.//repo', $node)[0]->nodeValue;
                $typeList['branch'] = $this->xpath->query('.//branch', $node)[0]->nodeValue;
                break;
        }
        return $typeList;
    }

    /**
     * @param \DOMNode $node
     * @return array|array[]
     */
    private function getServices(\DOMNode $node): array
    {
        $serviceList = [];
        $services = $this->xpath->query('.//services/service', $node);
        foreach ($services as $service) {
            $serviceList[$service->getAttribute('name')] = $this->getServiceArguments($service);
        }
        return $serviceList;
    }

    /**
     * @param \DOMNode $node
     * @return array|array[]
     */
    private function getServiceArguments(\DOMNode $node): array
    {
        $argumentList = [];
        $arguments = $this->xpath->query('.//arguments/argument', $node);
        foreach ($arguments as $argument) {
            $argumentList[$argument->getAttribute('name')] = $argument->nodeValue;
        }
        return [
            'arguments' => $argumentList
        ];
    }

    /**
     * @param \DOMNode $node
     * @param string $type
     * @return array
     */
    private function getCommands(\DOMNode $node, string $type): array
    {
        $commandList = [];
        $commands = $this->xpath->query(".//$type/command", $node);
        foreach ($commands as $command) {
            $commandList[$command->getAttribute('name')] = $this->buildCommand($command);
        }
        return $commandList;
    }

    /**
     * @param \DOMNode $node
     * @return array
     */
    private function buildCommand(\DOMNode $node): array
    {
        $commandList = [];
        $commandList['type'] = $node->getAttribute('container');
        // Without full path PHP commands are relative to Magento base folder
        if ($commandList['type'] == 'php') {
            $commandList['arguments'] = $this->getCommandArguments($node, $this->path);
            $commandList['version'] = $node->getAttribute('version');
        } else {
            $commandList['arguments'] = $this->getCommandArguments($node);
        }
        return $commandList;
    }

    /**
     * @param \DOMNode $node
     * @param string $path
     * @return array|array[]
     */
    private function getCommandArguments(\DOMNode $node, $path = ''): array
    {
        $argumentList = [];
        $arguments = $this->xpath->query('.//arguments/argument', $node);
        if ($path) {
            $argumentList[] = $path . DIRECTORY_SEPARATOR . $node->getAttribute('path');
        } else {
            $argumentList[] = $node->getAttribute('path');
        }
        foreach($arguments as $argument) {
            if (!$glue = $argument->getAttribute('glue')) {
                $glue = self::CONFIG_GLUE;
            }
            if ($argument->getAttribute('name')) {
                $argumentList[] = "{$argument->getAttribute('name')}$glue{$argument->nodeValue}";
            } else {
                $argumentList[] = $argument->nodeValue;
            }
        }
        return $argumentList;
    }
}
