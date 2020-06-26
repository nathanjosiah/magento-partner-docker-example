<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Config;

use Magento\UpgradeTool\Config\Dom;

class Command
{
    const GLUE = ' ';

    /**
     * @var \DOMNode
     */
    private \DOMNode $commandNode;

    /**
     * @var string
     */
    private string $container;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var \Magento\UpgradeTool\Config\Dom
     */
    private $dom;

    /**
     * Command constructor.
     * @param \Magento\UpgradeTool\Config\Dom $dom
     * @param \DOMNode $commandNode
     */
    public function __construct(DOM $dom, \DOMNode $commandNode)
    {
        $this->dom = $dom;
        $this->commandNode = $commandNode;
        $this->container = $commandNode->getAttribute('container');
        $this->path = $commandNode->getAttribute('path');
        $this->name = $commandNode->getAttribute('name');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContainer(): string
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function buildCommand(): string
    {
        $arguments = $this->getArguments($this->commandNode);
        return "{$this->path} {$this->buildParameters($arguments)}";
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

    /**
     * @param \DOMNode $node
     * @return array
     */
    public function getArguments(\DOMNode $node): array
    {
        $argumentNodes = $this->dom->query('.//arguments/argument', $node);
        $arguments = [];
        foreach ($argumentNodes as $argumentNode) {
            $arguments[$argumentNode->getAttribute('key')] = [
                'name' => $argumentNode->getAttribute('name'),
                'value' => $argumentNode->nodeValue,
                'glue' => $argumentNode->getAttribute('glue')
            ];
        }
        return $arguments;
    }
}
