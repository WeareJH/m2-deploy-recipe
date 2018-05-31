<?php

namespace Deployer;

desc('Pre-connect to SSH');
task('ssh:preconnect', function () {
    run('echo "Connection Established..."');
});
