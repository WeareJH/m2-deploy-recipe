<?php

use function Deployer\desc;
use function Deployer\runLocally;
use function Deployer\task;

desc('Checkout deployment branch');
task('git:checkout', function () {
    runLocally('cd {{local_src}} && {{local_bin/git}} checkout {{branch}}');
});
