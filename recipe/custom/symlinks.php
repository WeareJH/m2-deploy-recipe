<?php

namespace Deployer;

desc('Create symlinks defined by configuration');
task('symlinks:local:create', function () {
    /** @var array $symlinks */
    $symlinks = get('symlinks');

    foreach ($symlinks as $src => $dest) {
        runLocally(sprintf('ln -sf %s %s', $src, $dest));
    }
});
