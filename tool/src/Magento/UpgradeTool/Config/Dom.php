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

    public function read(string $xml)
    {
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
        return $dom;
    }

    public function getDom()
    {
        return $this->dom;
    }

    public function query($expression, $node = null): \DOMNodeList
    {
        if ($node) {
            return $this->xpath->query($expression, $node);
        } else {
            return $this->xpath->query($expression);
        }
    }

    public function getTest($testName): \DOMNode
    {
        return $this->query("//config/tests/test[@name='$testName']")->item(0);
    }

    public function getFromVersion($node): \DOMNode
    {
        return $this->query('//fromVersion', $node)->item(0);
    }

    public function getPackage($node): string
    {
        return $this->query('//package', $node)->item(0)->nodeValue;
    }

    public function getVersion($node): string
    {
        return $this->query('//version', $node)->item(0)->nodeValue;
    }

    public function getPath($node): string
    {
        return $this->query('//path', $node)->item(0)->nodeValue;
    }

    public function getAfter($node): \DOMNodeList
    {
        return $this->query('//after/command');
    }

    public function getBefore($node): \DOMNodeList
    {
        return $this->query('//before/command');
    }
}
