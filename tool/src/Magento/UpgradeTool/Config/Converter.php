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
        //$config = [];
        //$xpath = new \DOMXPath($document);
        //$testNodes = $xpath->query('/tests');
        //foreach ($testNodes as $testNode) {
        //    $config[$testNode->getAttribute('name')] = [/* etc */];
        //}
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
                                'type' => 'php',
                                'version' => '7.3',
                                'arguments' => [
                                    '/magento/magento-ce/bin/magento',
                                    'setup:install',
                                    '--admin-firstname=Nathan',
                                    '--admin-lastname=Smith',
                                    '--admin-email=foo@example.com',
                                    '--admin-user=admin',
                                    '--admin-password=123123q',
                                    '--base-url=http://magento/',
                                    '--db-host=db',
                                    '--db-name=main',
                                    '--db-user=root',
                                    '--db-password=secretpw',
                                    '--currency=USD',
                                    '--timezone=America/Chicago',
                                    '--language=en_US',
                                    '--use-rewrites=1',
                                    '--backend-frontname=admin',
                                ]
                            ],
                            [
                                'type' => 'php',
                                'version' => '7.3',
                                'arguments' => [
                                    '/magento/magento-ce/bin/magento',
                                    'de:mo:se production'
                                ]
                            ],
                            [
                                'type' => 'php',
                                'version' => '7.3',
                                'arguments' => [
                                    '/magento/magento-ce/bin/magento',
                                    'config:set',
                                    'admin/security/admin_account_sharing',
                                    '1',
                                ]
                            ],
                            [
                                'type' => 'php',
                                'version' => '7.3',
                                'arguments' => [
                                    '/magento/magento-ce/bin/magento',
                                    'config:set',
                                    'admin/security/use_form_key',
                                    '0',
                                ]
                            ],
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
