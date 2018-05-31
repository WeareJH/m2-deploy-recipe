<h1 align="center">M2 Deploy Recipe</h1>

## Setup

### Deployer Configuration

Create a new file `deploy.php` in the project root (if it does not already exist)

In this file add the contents below and edit/add the host/s as required

```php
<?php

namespace Deployer;

require 'recipe/mage.php';

host('server.hostname')
    ->port(22)
    ->stage('dev')
    ->user('www-data')
    ->set('branch', 'develop')
    ->set('keep_releases', 1)
    ->set('deploy_path', '/some/deploy/path')
```
