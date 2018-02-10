<?php

namespace Deployer;

use Deployer\Ssh\Arguments;
use Deployer\Task\Context;

set('zip_path', sys_get_temp_dir() . '/deploy.tgz');

task('deploy:zip:create', function () {
    runLocally('cd {{local_src}} && tar -czf {{zip_path}} .');
});

task('deploy:zip:upload', function () {
    $server = Context::get()->getHost();
    $sshPort = $server->getPort();
    $controlPath = $server->isMultiplexing()
        ? '-o ControlPath=' . (new Arguments())->withMultiplexing($server)->getOption('ControlPath')
        : '';

    runLocally("scp -P $sshPort $controlPath {{zip_path}} $server:{{release_path}}");
});

task('deploy:zip:unzip', function () {
    run('cd {{release_path}} && tar -xzf deploy.tgz && rm deploy.tgz');
});
