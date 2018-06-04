<?php

namespace Deployer;

require 'recipe/common.php';
require 'recipe/rsync.php';

// Config
require __DIR__ . '/config.php';
// Upgrade Deployments
require __DIR__ . '/custom/deploy.php';
// Zip Deployments
require __DIR__ . '/custom/zip.php';
// Custom Magento Commands
require __DIR__ . '/custom/magento2.php';
require __DIR__ . '/custom/magentoInstall.php';
// Other Custom Commands
require __DIR__ . '/custom/composer.php';
require __DIR__ . '/custom/ssh.php';
require __DIR__ . '/custom/akoova.php';

desc('Build Magento 2 production assets');
task('build', [
    'magento:local:setup:static-content:deploy',
    'magento:local:setup:di:compile'
]);

desc('Bundle Magento 2 into tarball');
task('bundle', [
    'deploy:zip:create'
]);

desc('Deploy release to sonassi server');
task('sonassi', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:zip:upload',
    'deploy:zip:unzip',
    'deploy:unlock',
    'success'
]);

desc('Deploy files to server [deprecated, use the sonassi task]');
task('deploy', [ 'sonassi' ]);

desc('Deploy release to akoova server');
task('akoova', [
    'akoova:zip:upload',
    'akoova:trigger:deploy',
    'akoova:deploy:status',
    'success'
]);


desc('Atomic release');
task('release:atomic', [
    'deploy:lock',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
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
task('release:backup', [
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
