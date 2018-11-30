<?php

namespace Deployer;

desc('Deploy Magento with Upgrade');
task('deploy:magento:upgrade', [
    'magento:maintenance:enable',
    'magento:setup:upgrade:keep-generated',
    'magento:cache:flush',
    'magento:maintenance:disable'
]);

desc('Deploy Magento with DB Backup & Upgrade');
task('deploy:magento:backup', [
    'magento:maintenance:enable',
    'magento:setup:backup:db',
    'magento:setup:upgrade:keep-generated',
    'magento:cache:flush',
    'magento:maintenance:disable'
]);
