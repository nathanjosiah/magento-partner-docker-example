<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Config;

/**
 * replace with magento reader. this is for example.
 */
class Dom
{
    private $dom;
    private $xpath;

    /**
     * Read configuration file
     * @param string $xml
     * @return \DOMDocument
     */
    public function read(string $xml): \DOMDocument
    {
        if (!$this->dom) {
            $dom = new \DOMDocument();
            $useErrors = libxml_use_internal_errors(true);
            $res = $dom->loadXML($xml);
            if (!$res) {
                libxml_use_internal_errors($useErrors);
                throw new \RuntimeException('Couldn\'t parse.');
            }
            libxml_use_internal_errors($useErrors);
            $this->dom = $dom;
            $this->xpath = new \DOMXPath($dom);
        }

        return $this->dom;
    }

    /**
     * Xpath query
     * @param $expression
     * @param null $node
     * @return \DOMNodeList
     */
    public function query(string $expression, $node = null): \DOMNodeList
    {
        if ($node) {
            return $this->xpath->query($expression, $node);
        } else {
            return $this->xpath->query($expression);
        }
    }

    public function getTest(string $testName): \DOMNode
    {
        return $this->query("//config/tests/test[@name='$testName']")->item(0);
    }

    public function getFromVersion(\DOMNode $node): \DOMNode
    {
        return $this->query('.//fromVersion', $node)->item(0);
    }

    public function getPackage(\DOMNode $node): string
    {
        return $this->query('.//package', $node)->item(0)->nodeValue;
    }

    public function getVersion(\DOMNode $node): string
    {
        return $this->query('.//version', $node)->item(0)->nodeValue;
    }

    public function getPath(\DOMNode $node): string
    {
        return $this->query('.//path', $node)->item(0)->nodeValue;
    }

    public function getAfter(\DOMNode $node): \DOMNodeList
    {
        return $this->query('.//after/command', $node);
    }

    public function getBefore(\DOMNode $node): \DOMNodeList
    {
        return $this->query('.//before/command', $node);
    }

    public function getArguments(\DOMNode $node): array
    {
        $argumentNodes = $this->query('.//arguments/argument', $node);
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
