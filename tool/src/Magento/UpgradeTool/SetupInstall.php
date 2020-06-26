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
        $composerUsername = getenv('MAGE_COMPOSER_USERNAME');
        $composerPassword = getenv('MAGE_COMPOSER_PASSWORD');
        $userPassword = getenv('MAGE_ADMIN_PASSWORD');
        $adminEmail = getenv('MAGE_ADMIN_EMAIL');

        if (file_exists('/magento/magento-ce/vendor')) {
            $this->log('Magento is already installed');
            return 0;
        }

        $this->log('Installing Magento 2.3.5 package.');
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

        $this->log('Running highly unsafe permission change to 777 for everything for now.');
        `chmod -R 777 /magento/magento-ce`;

        // TODO These need to be moved to the config and processed dynamically as described
        $this->log('Initializing Magento');

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

        $this->log('Configuring magento for mftf');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/admin_account_sharing 1');
        $this->runPhp('php /magento/magento-ce/bin/magento config:set admin/security/use_form_key 0');

        return 0;
    }
}
