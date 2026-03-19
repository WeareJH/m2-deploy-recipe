<?php

namespace Deployer;

// Official deployer 8.x magento2 recipe
// https://deployer.org/docs/8.x/recipe/magento2
// https://github.com/deployphp/deployer/blob/go:master/recipe/magento2.php
require 'recipe/magento2.php';

// Compression: 'gz' (default, universal) or 'zstd' (faster, smaller, requires zstd on host)
set('artifact_compression', 'gz');

// Override default artifact filename with git hash for meaningful filenames
set('artifact_file', function () {
    $hash = runLocally('git rev-parse --short HEAD');
    $ext = get('artifact_compression') === 'zstd' ? 'tar.zst' : 'tar.gz';
    return "$hash.$ext";
});

// JH custom recipes
require __DIR__ . '/custom/akoova.php';
require __DIR__ . '/custom/hyva.php';
require __DIR__ . '/custom/go-scd.php';

// Sonassi/Akoova handle OPcache purge via their own mechanisms
task('cachetool:clear:opcache')->disable();

// Override artifact:package to support zstd compression
task('artifact:package', function () {
    if (!test('[ -f {{artifact_excludes_file}} ]')) {
        throw new \Deployer\Exception\GracefulShutdownException(
            "No artifact excludes file provided, provide one at artifacts/excludes or change location",
        );
    }

    $compression = get('artifact_compression');
    $flag = $compression === 'zstd' ? '--zstd' : '-z';

    run("{{bin/tar}} --exclude-from={{artifact_excludes_file}} $flag -cf {{artifact_path}} -C {{release_or_current_path}} .");
});

// Override artifact:extract to support zstd compression (core recipe hardcodes -z)
task('artifact:extract', function () {
    $compression = get('artifact_compression');
    $flag = $compression === 'zstd' ? '--zstd' : '-z';

    run("{{bin/tar}} $flag -xpf {{release_path}}/{{artifact_file}} -C {{release_path}}");
    run("rm -rf {{release_path}}/{{artifact_file}}");
});

after('deploy:failed', 'deploy:unlock');