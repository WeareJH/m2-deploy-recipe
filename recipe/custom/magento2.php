<?php

use function Deployer\desc;
use function Deployer\run;
use function Deployer\task;
use function Deployer\get;

desc('Deploy assets');
task('magento:deploy:assets', function () {
    $locales = get('locales') ?: [];
    run(sprintf('{{bin/php}} {{release_path}}/bin/magento setup:static-content:deploy %s', implode(' ', $locales)));
});

desc('Set Production Mode');
task('magento:deploy:mode:set:production', function () {
    run('{{bin/php}} {{release_path}}/bin/magento deploy:mode:set production');

    // Copy env file into shared files
    run('mkdir -p {{deploy_path}}/shared/app/etc');
    run('cp {{release_path}}/app/etc/env.php {{deploy_path}}/shared/app/etc/env.php');
});
