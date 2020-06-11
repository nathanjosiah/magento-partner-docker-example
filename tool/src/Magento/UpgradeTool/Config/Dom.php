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

        return $dom;
    }
}
