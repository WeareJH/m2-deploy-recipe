<?php

namespace Deployer;

use Deployer\Host\Localhost;
use Deployer\Task\Context;

desc('Rsync local->remote');
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

    $host = Context::get()->getHost();
    if ($host instanceof Localhost) {
        runLocally("rsync -{$config['flags']} {{rsync_options}}{{rsync_excludes}}{{rsync_includes}}{{rsync_filter}} '$src/' '$dst/'", ['timeout' => $config['timeout']]);
        return;
    }

    $hostname = $host->getHostname();
    $user     = !$host->getUser() ? '' : $host->getUser() . '@';

    $sshArguments = $host->getSshArguments();
    $sshArguments = $host->isMultiplexing() ? $sshArguments->withMultiplexing($host) : $sshArguments;
    $sshArguments = $host->getPort() ? $sshArguments->withFlag('-p', $host->getPort()) : $sshArguments;
    $sshArguments = $host->getIdentityFile() ? $sshArguments->withFlag('-i', $host->getIdentityFile()) : $sshArguments;

    runLocally(
        "rsync -{$config['flags']} -e 'ssh $sshArguments' {{rsync_options}}{{rsync_excludes}}{{rsync_includes}}{{rsync_filter}} '$src/' '$user$hostname:$dst/'",
        ['timeout' => $config['timeout']]
    );
});
