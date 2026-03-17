<?php

namespace Deployer;

// Override static content deploy with Go-based binary (elgentos/magento2-static-deploy)
// Drop-in replacement: handles Hyva themes via fast Go copy, auto-delegates Luma to bin/magento
// https://github.com/elgentos/magento2-static-deploy
set('bin/static-deploy', 'static-deploy');

desc('Deploy static assets using Go binary (elgentos/magento2-static-deploy)');
task('magento:deploy:assets', function () {
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
        '{{bin/static-deploy}} -f -a frontend -a adminhtml --content-version={{content_version}} '
        . '{{static_deploy_options}} {{static_content_locales}} ' . trim($themesToCompile) . ' -j {{static_content_jobs}}'
    );
});