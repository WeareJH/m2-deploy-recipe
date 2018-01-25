***REMOVED***

***REMOVED***

desc('Install Magento Instance');
task('magento:install', function () {
    $host        = ask('DB Host');
    $db          = ask('DB Name (must be created)');
    $dbUser      = ask('DB User');
    $dbPass      = ask('DB Password');
    $baseUrl     = ask('Base URL');
    $secureURl   = str_replace('http://', 'https://', $baseUrl);
    $useSecure   = (int) askConfirmation('Use secure?');
    $adminFirst  = ask('Admin First Name');
    $adminLast   = ask('Admin Last Name');
    $adminEmail  = ask('Admin Email');
    $adminUser   = ask('Admin Username');
    $adminPass   = ask('Admin Password');
    $useRabbitMq = askConfirmation('Use RabbitMQ?');
    $ampqHost    = !$useRabbitMq ?: ask('RabbitMQ Host');
    $ampqPort    = !$useRabbitMq ?: ask('RabbitMQ Port');
    $ampqUser    = !$useRabbitMq ?: ask('RabbitMQ User');
    $ampqPass    = !$useRabbitMq ?: ask('RabbitMQ Password');
    $language    = ask('Language', 'en_GB');
    $timezone    = ask('Timezone', 'Europe/London');

    $command  = 'cd {{release_path}} && {{bin/php}} bin/magento setup:install';
    $command .= " --language=$language";
    $command .= " --timezone=$timezone";
    $command .= " --db-host=$host";
    $command .= " --db-name=$db";
    $command .= " --db-user=$dbUser";
    $command .= " --db-password=$dbPass";
    $command .= " --base-url=$baseUrl";
    $command .= " --base-url-secure=$secureURl";
    $command .= " --admin-firstname=$adminFirst";
    $command .= " --admin-lastname=$adminLast";
    $command .= " --admin-email=$adminEmail";
    $command .= " --admin-user=$adminUser";
    $command .= " --admin-password=$adminPass";
    $command .= ' --backend-frontname=admin';
    $command .= " --use-secure=$useSecure";
    $command .= " --use-secure-admin=$useSecure ##RABBIT";
    $command .= " --amqp-host=$ampqHost";
    $command .= " --amqp-port=$ampqPort";
    $command .= " --amqp-user=$ampqUser";
    $command .= " --amqp-password=$ampqPass";
    $command .= ' --amqp-virtualhost=/ ##RABBIT';
    $command .= ' --session-save=db';
    $command .= ' --cleanup-database -vvv';

    $regex = $useRabbitMq ? '/##RABBIT/s' : '/##RABBIT.*##RABBIT/s';

    writeln('<info>Running Installation...</info>');
    run(preg_replace($regex, '', $command));
});
