***REMOVED***
/** Override to do relative symlink */

use function Deployer\cd;
use function Deployer\desc;
use function Deployer\parse;
use function Deployer\run;
use function Deployer\set;
use function Deployer\get;
use function Deployer\task;

set('release_path', function () {
    $releaseExists = run("if [ -h {{deploy_path}}/release ]; then echo 'true'; fi")->toBool();
    if ($releaseExists) {
        $link = run("cd {{deploy_path}} && readlink release")->toString();
        return substr($link, 0, 1) === '/' ? $link : get('deploy_path') . '/' . $link;
    } else {
        return get('current_path');
    }
});

desc('Prepare release');
task('deploy:release', function () {
    cd('{{deploy_path}}');

    // Clean up if there is unfinished release.
    $previousReleaseExist = run("if [ -h release ]; then echo 'true'; fi")->toBool();

    if ($previousReleaseExist) {
        run('rm -rf "$(readlink release)"'); // Delete release.
        run('rm release'); // Delete symlink.
    }

    $releaseName = get('release_name');

    // Fix collisions.
    $i = 0;
    while (run("if [ -d {{deploy_path}}/releases/$releaseName ]; then echo 'true'; fi")->toBool()) {
        $releaseName .= '.' . ++$i;
        set('release_name', $releaseName);
    }

    $releasePath = parse("{{deploy_path}}/releases/{{release_name}}");

    // Metainfo.
    $date = run('date +"%Y%m%d%H%M%S"');

    // Save metainfo about release.
    run("echo '$date,{{release_name}}' >> .dep/releases");

    // Make new release.
    run("mkdir $releasePath");

    // Do relative symlink
    relative_symlink($releasePath, '{{deploy_path}}/release');
});
