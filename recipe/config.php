<?php

namespace Deployer;

set('local_src', '/root/build');

set('locales', ['en_GB', 'en_US']);

set('composer_options', '-o --no-dev --prefer-dist');

set('shared_files', [
   'app/etc/env.php'
]);

set('shared_dirs', [
    'var/log',
    'var/report',
    'var/backups',
    'pub/media',
    'pub/sitemap',
]);

set('writable_mode', 'chmod');
set('writable_dirs', [
    'var',
    'pub/static',
]);

set('build_exclusions', [
    './.git',
    './.docker',
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
    './tsconfig.json',
    './tslint.json',
    './crossbow.yaml',
    './fe',
    './node_modules'
]);

set('local_bin/php', function () {
    return runLocally('which php');
});
