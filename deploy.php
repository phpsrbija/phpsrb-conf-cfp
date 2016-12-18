<?php
namespace Deployer;

require 'recipe/common.php';

date_default_timezone_set('UTC');

$config = require __DIR__ . '/deploy/config.php';

serverList($config['servers']);

foreach ($config['parameters'] as $key => $value) {
    set($key, $value);
}

set('copy_dirs', [
    'web/uploads',
]);
set('writable_dirs', [
    'log',
    'web/uploads',
    'vendor/ezyang/htmlpurifier',
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

task('cleanup', function () {
    $releases = get('releases_list');

    $keep = get('keep_releases');

    if ($keep === -1) {
        // Keep unlimited releases.
        return;
    }

    while ($keep - 1 > 0) {
        array_shift($releases);
        --$keep;
    }

    foreach ($releases as $release) {
        run("sudo rm -rf {{deploy_path}}/releases/$release");
    }

    run("cd {{deploy_path}} && if [ -e release ]; then sudo rm release; fi");
    run("cd {{deploy_path}} && if [ -h release ]; then sudo rm release; fi");
})->desc('Cleaning up old releases');

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:copy_dirs',
    'deploy:vendors',
    'phinx:migrate',
    'deploy:writable',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy project');

after('deploy', 'success');
