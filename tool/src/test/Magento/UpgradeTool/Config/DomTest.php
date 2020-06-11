<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);
namespace Magento\UpgradeTool\Config;

use PHPUnit\Framework\TestCase;

class DomTest extends TestCase
{
    public function testConfigIsParsed()
    {
        $dom = new Dom();
        $document = $dom->read(file_get_contents('/app/etc/config.xml'));
        $xpath = new \DOMXPath($document);
        $result = $xpath->query('//test[1]/@name');
        self::assertSame('BasicUpgradeTest', $result->item(0)->nodeValue);
    }
}
