<?php

use function Deployer\desc;
use function Deployer\runLocally;
use function Deployer\task;

desc('Installing composer dependencies locally');
task('composer:install', function () {
    runLocally('cd {{local_src}} && {{env_vars}} {{local_bin/composer}} {{composer_options}}');
});
