<?php

namespace Deployer;

// Official deployer 8.x magento2 recipe
// https://deployer.org/docs/8.x/recipe/magento2
// https://github.com/deployphp/deployer/blob/go:master/recipe/magento2.php
require 'recipe/magento2.php';

// Override default artifact filename with git hash for meaningful filenames
set('artifact_file', function () {
    $hash = runLocally('git rev-parse --short HEAD');
    return "$hash.tar.gz";
});

// JH custom recipes
require __DIR__ . '/custom/akoova.php';
require __DIR__ . '/custom/hyva.php';
require __DIR__ . '/custom/go-scd.php';

// Sonassi/Akoova handle OPcache purge via their own mechanisms
task('cachetool:clear:opcache')->disable();

after('deploy:failed', 'deploy:unlock');