<?php

namespace Deployer;

use Deployer\Ssh\Arguments;
use Deployer\Task\Context;

set('bundle_name', function () {
    $rev = runLocally('git rev-parse HEAD');
    return $rev . '.tgz';
});

set('zip_path', sys_get_temp_dir() . '/{{bundle_name}}');

set('exclusions', function () {
    return implode(' ', array_map(function ($exclusion) {
        return sprintf('--exclude="%s"', $exclusion);
    }, get('build_exclusions')));
});

task('deploy:zip:create', function () {
    runLocally('cd {{local_src}} && tar {{exclusions}} -czf {{zip_path}} .');
});

task('deploy:zip:upload', function () {
    $server = Context::get()->getHost();
    $sshPort = $server->getPort();
    $serverArgs = $server->getSshArguments();

    $arguments = $server->isMultiplexing()
        ? '-o ControlPath=' . (new Arguments())->withMultiplexing($server)->getOption('ControlPath')
        : '';

    if ($strictHostKeyChecking = $serverArgs->getOption('StrictHostKeyChecking')) {
        $arguments .= sprintf(' -o StrictHostKeyChecking=%s', $strictHostKeyChecking);
    }

    if ($userKnownHostsFile = $serverArgs->getOption('UserKnownHostsFile')) {
        $arguments .= sprintf(' -o UserKnownHostsFile=%s', $userKnownHostsFile);
    }

    runLocally("scp -P $sshPort $arguments {{zip_path}} $server:{{release_path}}");
});

task('deploy:zip:unzip', function () {
    run('cd {{release_path}} && tar -xzf deploy.tgz && rm {{bundle_name}}');
});
