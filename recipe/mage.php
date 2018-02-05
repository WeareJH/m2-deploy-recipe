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

/**
 * Patch rsync recipe SSH Args until...
 * @see https://github.com/deployphp/deployer/pull/1531
 */
task('rsync', function () {
    $config = get('rsync');
    $src = get('rsync_src');
    while (is_callable($src)) {
        $src = $src();
    }
    if (!trim($src)) {
        // if $src is not set here rsync is going to do a directory listing
        // exiting with code 0, since only doing a directory listing clearly
        // is not what we want to achieve we need to throw an exception
        throw new \RuntimeException('You need to specify a source path.');
    }
    $dst = get('rsync_dest');
    while (is_callable($dst)) {
        $dst = $dst();
    }
    if (!trim($dst)) {
        // if $dst is not set here we are going to sync to root
        // and even worse - depending on rsync flags and permission -
        // might end up deleting everything we have write permission to
        throw new \RuntimeException('You need to specify a destination path.');
    }
    /** @var \Deployer\Host\Host $server */
    $server = \Deployer\Task\Context::get()->getHost();
    if ($server instanceof \Deployer\Host\Localhost) {
        runLocally("rsync -{$config['flags']} {{rsync_options}}{{rsync_excludes}}{{rsync_includes}}{{rsync_filter}} '$src/' '$dst/'", $config);
        return;
    }
    $host = $server->getRealHostname();
    $port = $server->getPort() ? ' -p' . $server->getPort() : '';
    $sshArguments = $server->getSshArguments();
    $sshArguments = $server->isMultiplexing() ? $sshArguments->withMultiplexing($server) : $sshArguments;
    $user = !$server->getUser() ? '' : $server->getUser() . '@';
    runLocally("rsync -{$config['flags']} -e 'ssh$port $sshArguments' {{rsync_options}}{{rsync_excludes}}{{rsync_includes}}{{rsync_filter}} '$src/' '$user$host:$dst/'", $config);
});
