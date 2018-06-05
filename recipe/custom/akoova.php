<?php

namespace Deployer;

use Deployer\Ssh\Arguments;
use Deployer\Task\Context;

task('akoova:zip:upload', function () {
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

    runLocally("scp -P $sshPort $arguments {{zip_path}} $server:{{deploy_path}}");
});

desc('Touch file to start deployment on Akoova');
task('akoova:trigger:deploy', function () {
    run('touch {{ deploy_path }}/deploy-{{ bundle_name }}');
});

desc('Touch file to start rollback on Akoova');
task('akoova:trigger:rollback', function () {
    if (!input()->hasOption('tag')) {
        throw new \RuntimeException(
            'Rollback requires "--tag" option to be defined, provided by Akoova on deployment, e.g. --tag="1.0.0.0"'
        );
    }

    $rollbackTag = input()->getOption('tag');
    run('touch {{ deploy_path }}/rollback-' . $rollbackTag);
});

set('deploy_status_wait', 180);

desc('Poll for deployment status');
task('akoova:deploy:status', function () {
    $wait = get('deploy_status_wait');
    $time = time();

    while (time() - $time < $wait) {
        // Checks for the removal of deploy trigger
        if (test('[ ! -f {{ deploy_path }}/deploy-{{ bundle_name }} ]')) {
            return true;
        }
        sleep(10);
    }

    throw new \RuntimeException('Gave up waiting after "'. $wait .' seconds"  - presumed failed.');
});
