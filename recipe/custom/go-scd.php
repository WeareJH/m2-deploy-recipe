<?php

namespace Deployer;

// --- Elgentos static content deploy (Go binary) ---
// https://github.com/elgentos/magento2-static-deploy
//
// Drop-in replacement for Magento's setup:static-content:deploy,
// 230-380x faster for Hyva themes (Go-based file copying).
//
// To use in project deploy.php:
//   task('magento:deploy:assets')->disable();
//   after('magento:compile', 'go:deploy:assets');
//
set('bin/static-deploy', 'static-deploy');

desc('Deploy static assets using Go binary (elgentos/magento2-static-deploy)');
task('go:deploy:assets', function () {
    $locales = get('static_content_locales');
    $jobs = get('static_content_jobs');
    $options = get('static_deploy_options');

    $themesToCompile = '';
    if (count(get('magento_themes')) > 0) {
        $themes = array_is_list(get('magento_themes'))
            ? get('magento_themes')
            : array_keys(get('magento_themes'));
        foreach ($themes as $theme) {
            $themesToCompile .= ' -t ' . $theme;
        }
    }

    run(
        '{{bin/static-deploy}} -f --content-version={{content_version}} '
        . "$options $locales " . trim($themesToCompile) . " -j $jobs"
    );
});