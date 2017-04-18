<h1 align="center">M2 Deploy Recipe</h1>

## Setup

#### Composer Plugin Setup

Use `bamarni/composer-bin-plugin` to pull in the required Deployer libs without impacted the project dependencies.

```
$ composer require bamarni/composer-bin-plugin ^1.1
```

Add the following snippet to your `composer.json` `scripts` block

```
"post-install-cmd": "@composer bin all install --ansi"
```

Run `mkdir -p vendor-bin/deployer` in the project root

Create a `composer.json` file in that directory with the contents below

```json
{
    "repositories": [
        { "type": "vcs", "url": "git@github.com:mikeymike/deployer.git" },
        { "type": "vcs", "url": "git@github.com:WeareJH/m2-deploy-recipe.git" }
    ],
    "require": {
        "deployer/deployer": "dev-feature/configurable-ssh-multiplexing",
        "wearejh/m2-deploy-recipe": "dev-master"
    },
    "minimum-stability": "dev"
}
```

Run `composer update` in the new directory


#### Deployer Configuration

Create a new file `deploy.php` in the project root (if it does not already exist)

In this file add the contents below and edit/add the host/s as required

```php
<?php

use function Deployer\host;

require_once __DIR__ . '/vendor-bin/deployer/vendor/wearejh/m2-deploy-recipe/recipe/bootstrap.php';

host('dh1.c309.sonassihosting.com')
    ->port(3022)
    ->stage('dev')
    ->user('www-data')
    ->set('branch', 'feature/deployments')
    ->set('keep_releases', 1)
    ->set('deploy_path', '/pathtodeploy')
    ->multiplexing()
    ->addSshFlag('-q')
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->addSshOption('ControlPersist', '600');
```
