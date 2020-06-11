<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\UpgradeTool;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupInstall extends AbstractCommand
{
    protected static $defaultName = 'setup:install';

    protected function configure()
    {
        $this->setDescription('Install magento');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $composerUsername = getenv('COMPOSER_USERNAME');
        $composerPassword = getenv('COMPOSER_PASSWORD');
        $userPassword = getenv('ADMIN_PASSWORD');
        $adminEmail = getenv('ADMIN_EMAIL');

        if (file_exists('/magento/magento-ce/vendor')) {
            $this->log('Magento is already installed');
            return 0;
        }

        $this->log('Installing Magento 2.3.4 package.');
        mkdir('/magento/.composer');

        file_put_contents('/magento/.composer/auth.json',
            <<<COMPOSER
{
  "http-basic": {
  "repo.magento.com": {
      "username": "${composerUsername}",
          "password": "${composerPassword}"
      }
  }
}
COMPOSER
        );
        $this->runPhp('composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition:2.3.5 /magento/magento-ce');

        $this->log('Running highly unsafe permision change to 777 for everything for now.');
        `chmod -R 777 /magento/magento-ce`;
        $this->log('Installing Magento');

        $this->runPhp('php /magento/magento-ce/bin/magento setup:install \
        --admin-firstname=Nathan \
        --admin-lastname=Smith \
        --admin-email=' . $adminEmail . ' \
        --admin-user=admin \
        --admin-password=' . $userPassword . ' \
        --base-url=http://magento/ \
        --db-host=db \
        --db-name=main \
        --db-user=root \
        --db-password=secretpw \
        --currency=USD \
        --timezone=America/Chicago \
        --language=en_US \
        --use-rewrites=1 \
        --backend-frontname=\'admin\'
        ');

        $this->runPhp('php /magento/magento-ce/bin/magento de:mo:se production');

        // Only applies to magento 2.3.4
        //$this->log('Fixing bad composer requirement for 2.3.4');
        //$this->runPhp('composer require -d /magento/magento-ce symfony/http-foundation ^4.0');

        $this->log('Configuring magento for mftf');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf reset --hard');
        $this->runPhp('php /magento/magento-ce/vendor/bin/mftf build:project');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/admin_account_sharing 1');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/use_form_key 0');

        return 0;
    }
}
