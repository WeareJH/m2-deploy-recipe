<?php

namespace Deployer;

// --- Hyva Tailwind compilation ---
//
// Set 'hyva_themes' in project deploy.php to array of theme paths:
//   set('hyva_themes', ['Vendor/theme1', 'Vendor/theme2']);
//
// Each entry is relative to app/design/frontend/ and must contain
// a web/tailwind/package.json to be compiled.
//
set('hyva_themes', []);

desc('Compile Hyva Tailwind CSS');
task('hyva:deploy', function () {
    $themes = get('hyva_themes');
    if (empty($themes)) {
        return;
    }

    foreach ($themes as $theme) {
        $dir = "app/design/frontend/$theme/web/tailwind";
        if (test("[ -f $dir/package.json ]")) {
            run("cd $dir && npm ci && npm run build");
        } else {
            writeln("<comment>Skipping $theme — no package.json found</comment>");
        }
    }
});

before('magento:deploy:assets', 'hyva:deploy');