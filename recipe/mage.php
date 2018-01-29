<?php

namespace Deployer;

require 'recipe/common.php';
require 'recipe/rsync.php';

// Config
require __DIR__ . '/config.php';
// Upgrade Deployments
require __DIR__ . '/custom/deploy.php';
// Custom Magento Commands
require __DIR__ . '/custom/magento2.php';
require __DIR__ . '/custom/magentoInstall.php';
// Other Custom Commands
require __DIR__ . '/custom/composer.php';
require __DIR__ . '/custom/ssh.php';

desc('Deploy files to server');
task('deploy', [
    'composer:local:install',
    'magento:local:setup:static-content:deploy',
    'magento:local:setup:di:compile',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:unlock',
    'success'
]);

desc('Atomic release');
task('release:atomic', [
    'deploy:lock',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:magento:atomic',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

desc('Upgrade release');
task('release:upgrade', [
    'deploy:lock',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:magento:upgrade',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

desc('Upgrade db backup & release');
task('release:upgrade', [
    'deploy:lock',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:magento:backup',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

after('deploy:failed', 'deploy:unlock');
