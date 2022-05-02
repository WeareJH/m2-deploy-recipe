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
    $sshUser = $server->getRemoteUser();
    $sshPort = $server->getPort();
    $serverArgs = (array) $server->getSshArguments();

    $arguments = $server->getSshMultiplexing()
        ? '-o ControlPath=' . $server->getSshControlPath()
        : '';

    foreach ($serverArgs as $serverArg) {
        $arguments .= sprintf(' %s', $serverArg);
    }

    runLocally("scp -P $sshPort $arguments {{zip_path}} $sshUser@$server:{{release_path}}");
});

task('deploy:zip:unzip', function () {
    run('cd {{release_path}} && tar -xzf {{bundle_name}} && rm {{bundle_name}}');
});
