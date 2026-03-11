<?php

namespace Deployer;

// Official deployer 8.x magento2 recipe
// https://deployer.org/docs/8.x/recipe/magento2
// https://github.com/deployphp/deployer/blob/go:master/recipe/magento2.php
require 'recipe/magento2.php';

// JH custom recipes
require __DIR__ . '/custom/akoova.php';
require __DIR__ . '/custom/hyva.php';
require __DIR__ . '/custom/go-scd.php';

after('deploy:failed', 'deploy:unlock');