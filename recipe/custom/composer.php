<?php

namespace Deployer;

set('local_bin/composer', function () {
    $composer = runLocally('which composer')->toString();
    if (empty($composer)) {
        runLocally("cd {{release_path}} && curl -sS https://getcomposer.org/installer | {{local_bin/php}}");
        $composer = '{{local_bin/php}} {{local_release_path}}/composer.phar';
    }
    return $composer;
});

desc('Installing composer dependencies locally');
task('composer:local:install', function () {
    runLocally('cd {{local_src}} && {{local_bin/composer}} {{composer_options}}');
});
