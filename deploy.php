<?php
namespace Deployer;

require 'recipe/common.php';

date_default_timezone_set('UTC');

$config = require __DIR__ . '/deploy/config.php';

serverList($config['servers']);

foreach ($config['parameters'] as $key => $value) {
    set($key, $value);
}

set('writable_dirs', [
    'log',
    'web/uploads',
]);
set('shared_files', [
    'config/production.yml',
    'config/development.yml',
    'phinx.yml',
]);

task('phinx:migrate', function () {
    $output = run('cd {{release_path}} && ./vendor/bin/phinx migrate');
    writeln('<info>' . $output . '</info>');
})->desc('Run database migrations');

task('phinx:migrate:rollback', function () {
    $output = run('cd {{release_path}} && ./vendor/bin/phinx rollback');
    writeln('<info>' . $output . '</info>');
})->desc('Rollback database migrations');

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'phinx:migrate',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy project');

after('deploy', 'success');
