<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool\Config;

/**
 *
 */
class Converter
{
    public function convert(\DOMDocument $document): array
    {
        // TODO convert real document to array
        $config = [];
        $xpath = new \DOMXPath($document);
        $testNodes = $xpath->query('/tests');
        foreach ($testNodes as $testNode) {
            $config[$testNode->getAttribute('name')] = [/* etc */];
        }
        //return $config;

        // fake implementation
        return [
            'tests' => [
                'BasicUpgradeTest' => [
                    'fromVersion' => [
                        'type' => 'composer',
                        'package' => 'magento/project-community-edition',
                        'version' => '2.3.5',
                        'services' => [
                            'php' => [
                                'version' => '7.3'
                            ]
                        ],
                        'after' => [
                            [
                                'type' => 'tool',
                                'arguments' => [
                                    'verify:mftf',
                                    'AdminLoginTest',
                                    '--php',
                                    '7.3'
                                ]
                            ]
                        ]
                    ],
                    'toVersion' => [
                        'type' => 'git',
                        'repo' => 'git@github.com:magento-borg/magento2ce',
                        'branch' => 'MC-12345',
                        'services' => [
                            'php' => [
                                'version' => '7.4'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
