<?php

use Magento\UpgradeTool\ObjectManager\ObjectArrayResolver;

// Should eventually be converted to xml
return [
    'preferences' => [
        \Psr\Log\LoggerInterface::class => \Symfony\Component\Console\Logger\ConsoleLogger::class,
        \Symfony\Component\Console\Input\InputInterface::class => \Symfony\Component\Console\Input\ArgvInput::class,
        \Symfony\Component\Console\Output\OutputInterface::class => \Symfony\Component\Console\Output\ConsoleOutput::class,
    ],
    'types' => [
        \Symfony\Component\Console\Output\Output::class => [
            'parameters' => [
                'verbosity' => 9999999
            ]
        ],
        \Magento\UpgradeTool\ApplicationFactory::class => [
            'parameters' => [
                'commands' => new ObjectArrayResolver(
                    [
                        \Magento\UpgradeTool\RunTest::class,
                        \Magento\UpgradeTool\GetPhpTestVersion::class,
                        \Magento\UpgradeTool\RunMftfTest::class,
                        \Magento\UpgradeTool\RunUnitTests::class,
                        \Magento\UpgradeTool\SetupInstall::class,
                        \Magento\UpgradeTool\BuildEnvironment::class,
                    ]
                )
            ]
        ]
    ],
];