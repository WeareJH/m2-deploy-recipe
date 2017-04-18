***REMOVED***

use function Deployer\desc;
use function Deployer\run;
use function Deployer\task;

desc('Creating symlink to release');
task('deploy:symlink', function () {
    if (run('if [[ "$(man mv)" =~ "--no-target-directory" ]]; then echo "true"; fi')->toBool()) {
        run("mv -T {{deploy_path}}/release {{deploy_path}}/current");
    } else {
        // Atomic symlink does not supported.
        // Will use simple≤ two steps switch.

        relative_symlink('{{release_path}}', '{{deploy_path}}/current'); // Atomic override symlink.
        run("cd {{deploy_path}} && rm release"); // Remove release link.
    }
});
