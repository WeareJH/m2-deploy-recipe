<?php

namespace Deployer;

set('deploy_status_wait', 180);

desc('Deploy release to Akoova server');
task('akoova', [
    'akoova:upload',
    'akoova:trigger:deploy',
    'akoova:deploy:status',
    'deploy:success',
]);

task('akoova:upload', function () {
    $host = currentHost();
    $sshUser = $host->getRemoteUser();
    $hostName = $host->getHostname();
    $sshPort = $host->getPort() ?: 22;
    $serverArgs = $host->getSshArguments() ? (array) $host->getSshArguments() : [];

    $arguments = $host->getSshMultiplexing()
        ? '-o ControlPath=' . $host->getSshControlPath()
        : '';

    foreach ($serverArgs as $arg) {
        $arguments .= " $arg";
    }

    $artifactPath = get('artifact_path');
    $artifactFile = get('artifact_file');
    runLocally("scp -P $sshPort $arguments $artifactPath $sshUser@$hostName:{{deploy_path}}/$artifactFile");
});

desc('Touch file to start deployment on Akoova');
task('akoova:trigger:deploy', function () {
    $artifactFile = get('artifact_file');
    run("touch {{deploy_path}}/deploy-$artifactFile");
});

desc('Touch file to start rollback on Akoova');
task('akoova:trigger:rollback', function () {
    if (!input()->hasOption('tag')) {
        throw new \RuntimeException(
            'Rollback requires "--tag" option to be defined, provided by Akoova on deployment, e.g. --tag="1.0.0.0"'
        );
    }

    $rollbackTag = input()->getOption('tag');
    run('touch {{deploy_path}}/rollback-' . $rollbackTag);
});

desc('Poll for deployment status');
task('akoova:deploy:status', function () {
    $wait = get('deploy_status_wait');
    $artifactFile = get('artifact_file');
    $time = time();

    while (time() - $time < $wait) {
        if (test("[ ! -f {{deploy_path}}/deploy-$artifactFile ]")) {
            return true;
        }
        sleep(10);
    }

    throw new \RuntimeException('Gave up waiting after "' . $wait . ' seconds" - presumed failed.');
});