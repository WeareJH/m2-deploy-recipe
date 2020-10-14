<?php

namespace Deployer;

set('local_src', '/root/build');

set('composer_options', '-o --no-dev --prefer-dist');

// Key, Value = Source, Destination
// E.g ['.' => 'pub/eu'] to symlink pub to pub/eu for multistore
set('symlinks', []);

set('shared_files', []);

set('shared_dirs', []);

set('writable_mode', 'chmod');
set('writable_dirs', []);

set('build_exclusions', [
    './.DS_Store',
    './.git',
    './.gitignore',
    './.docker',
    './.dockerignore',
    './.circleci',
    './.user.ini',
    './.travis.yml',
    './.php_cs',
    './.htaccess*',
    './.phpstorm.meta.php',
    './*.sample',
    './*.md',
    './auth.json',
    './dev',
    './phpserver',
    './LICENSE*.txt',
    './COPYING.txt',
    './deploy.php',
    './docker-compose.*',
    './restore.sh',
    './env.sample.php',
    './phpunit.xml',
    './phpcs.xml',
    './.php_cs.dist',
    './tsconfig.json',
    './tslint.json',
    './crossbow.yaml',
    './fe',
    './node_modules'
]);

set('default_timeout', null); //foreverrrrrr

set('local_bin/php', function () {
    return runLocally('which php');
});
