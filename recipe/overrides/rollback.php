***REMOVED***

use function Deployer\desc;
use function Deployer\run;
use function Deployer\task;
use function Deployer\get;
use function Deployer\writeln;

desc('Rollback to previous release');
task('rollback', function () {
    $releases = get('releases_list');

    if (isset($releases[1])) {
        // Symlink to old release.
        relative_symlink("{{deploy_path}}/releases/{$releases[1]}", '{{deploy_path}}/current');

        // Remove release
        run("rm -rf {{deploy_path}}/releases/{$releases[0]}");

        if (isVerbose()) {
            writeln("Rollback to `{$releases[1]}` release was successful.");
        }
    } else {
        writeln("<comment>No more releases you can revert to.</comment>");
    }
});
