<?php

namespace Deployer;

set('akoova_tmp_extract_path', sys_get_temp_dir() . '/akoova_build');

$deployName = get('akoova_deploy_name');
set('akoova_zip_file', sys_get_temp_dir() . "/{$deployName}.zip");

desc('Extract release tarball to convert to zip');

task('akoova:tarball:extract', function () {
    runLocally('mkdir -p {{ akoova_tmp_extract_path }} && cd {{ akoova_tmp_extract_path }} && tar -xzf {{ zip_path }}');
});

desc('Generate release zip for Akoova');
task('akoova:zip:create', function () {
    runLocally('cd {{ akoova__tmp_extract_path }} && zip -u -1 -r {{ akoova_zip_file }} .htaccess * -x .git');
});

desc('Upload release zip to Akoova manager');
task('akoova:zip:upload', function () {
    runLocally('scp -P {{ port }} {{ akoova_zip_file }} {{ user }}@{{ host }}:{{ deploy_path }}');
});

desc('Touch file to start deployment on Akoova');
task('akoova:trigger:create', function () {
    runLocally('ssh -p {{ port }} {{ user }}@{{ host }} touch {{ deploy_path }}/deploy-{{ akoova_deploy_name }}.zip');
});
