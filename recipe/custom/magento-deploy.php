<?php

use function Deployer\desc;
use function Deployer\task;

desc('Deploy Magento Atomic');
task('deploy:magento:atomic', [
    'magento:compile',
    'magento:deploy:assets',
    'magento:cache:flush'
]);

desc('Deploy Magento with Upgrade');
task('deploy:magento:upgrade', [
    'deploy:magento:atomic',
    'magento:maintenance:enable',
    'magento:upgrade:db',
    'magento:cache:flush',
    'magento:maintenance:disable'
]);
