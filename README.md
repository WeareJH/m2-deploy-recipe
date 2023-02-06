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

### Lighthouse configuration

There is a task that allows you to generate desktop and mobile Google Lighthouse results after running your 
tasks. The results are sent to a Slack channel. You need to set up a Slack bot integration that is allowed
to `file:write`

#### Requirements:
* `lighthouse` CLI tool (`npm install -g lighthouse`)
* `chromium`

#### Setup:

Add this snippet to your `deploy.php`

```php
set('lighthouse', (
    (new LighthouseConfig())->setTargetUrl('https://test-url.com')
    ->setBasicAuthToken('amg6Y3IfsasagsaagsaDEwbjUtdzByazgwNHQ=') // optional, if your site is protected
    ->setSlackAuthToken('xoxb-XXXXXXX-XXXXXXXXX-XXXXXXXXXXXXXX') // Slack bot token
    ->setSlackChannels('XXXXXXX') //Slack channels you want the message sent to, comma-separated
    ->setProjectSlug('project-name')
```

and trigger it after your deployment with
```
dep lighthouse:generate
```