<?php

use Magento\UpgradeTool\ObjectManager\ObjectArrayResolver;

// Should eventually be converted to xml
return [
    'types' => [
        \Magento\UpgradeTool\ApplicationFactory::class => [
            'parameters' => [
                'commands' => new ObjectArrayResolver(
                    [
                        \Magento\UpgradeTool\GetPhpTestVersion::class,
                        \Magento\UpgradeTool\RunTest::class,
                        \Magento\UpgradeTool\RunUnitTests::class,
                        \Magento\UpgradeTool\SetupInstall::class,
                        \Magento\UpgradeTool\VerifySetup::class,
                    ]
                )
            ]
        ]
    ],
];