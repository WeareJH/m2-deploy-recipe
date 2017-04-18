<?php

use function Deployer\set;
use function Deployer\get;

set('local_src', function () {
    // Initial path outside vendor
    $path     = __DIR__ . '/../../../../';
    $maxDepth = 2;

    // Step back through dirs until we find deploy.php
    while (!file_exists($path . 'deploy.php') || $maxDepth) {
        $path .= '../';
        $maxDepth--;
    }

    if (file_exists($path . 'deploy.php')) {
        return $path;
    }

    throw new \RuntimeException('Could not locate project root');
});

set('locales', ['en_GB', 'en_US']);

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
