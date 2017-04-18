<?php

use function Deployer\desc;
use function Deployer\task;

desc('Deploy files to server');
task('deploy', [
    'deploy:prepare',
    'git:checkout',
    'composer:install',
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
