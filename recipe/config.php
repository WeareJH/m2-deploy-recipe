<?php

namespace Deployer;

set('local_src', '/root/build');

set('locales', ['en_GB', 'en_US']);

set('composer_options', '-o --no-dev --prefer-dist');

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

set('rsync_src', get('local_src'));
set('rsync', [
    'exclude'       => [
        '/.docker',
        '/docker-compose*.yml',
        '/*.dockerfile',
        '/.dockerignore',
        '/.git',
        '/.gitignore',
        '/.deploy',
        '/deploy.php',
        '/vendor-bin',
        '/dev',
        '/test',
        '/phpserver',
        '/LICENSE*.txt',
        '/COPYING.txt',
        '/.user.ini',
        '/.travis.yml',
        '/.php_cs',
        '/.htaccess*',
        '/*.sample',
        '/.phpstorm.meta.php',
        '/*.md',
        '/circle.yml',
        '/.idea',
        '/phpunit.xml',
        '/phpcs.xml',
        '/auth.json',
        '/app/etc/env.php',
        'node_modules'
    ],
    'exclude-file'  => false,
    'include'       => [],
    'include-file'  => false,
    'filter'        => [],
    'filter-file'   => false,
    'filter-perdir' => false,
    'flags'         => 'rzcEL',
    'options'       => ['delete'],
    'timeout'       => 3600,
]);

set('local_bin/php', function () {
    return runLocally('which php')->toString();
});
