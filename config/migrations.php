<?php

use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WouterJ\EloquentBundle\Command\MigrateCommand;
use WouterJ\EloquentBundle\Command\MigrateFreshCommand;
use WouterJ\EloquentBundle\Command\MigrateInstallCommand;
use WouterJ\EloquentBundle\Command\MigrateRefreshCommand;
use WouterJ\EloquentBundle\Command\MigrateResetCommand;
use WouterJ\EloquentBundle\Command\MigrateRollbackCommand;
use WouterJ\EloquentBundle\Command\MigrateStatusCommand;
use WouterJ\EloquentBundle\Command\SeedCommand;
use WouterJ\EloquentBundle\Migrations\Migrator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('wouterj_eloquent.migrations.table', 'migrations')
        ->set('wouterj_eloquent.migration_path', '%kernel.project_dir%/migrations');

    $services = $container->services();

    $services->set('wouterj_eloquent.migrations.repository', DatabaseMigrationRepository::class)
        ->args([service('wouterj_eloquent.database_manager'), param('wouterj_eloquent.migrations.table')]);

    $services->set('wouterj_eloquent.migrator', Migrator::class)
        ->args([service('wouterj_eloquent.migrations.repository'), service('wouterj_eloquent.database_manager')]);

    $migrationPath = param('wouterj_eloquent.migration_path');
    $env = param('kernel.environment');
    $migrator = service('wouterj_eloquent.migrator');

    $services->set('wouterj_eloquent.commands.migrate', MigrateCommand::class)
        ->args([$migrator, $migrationPath, $env])
        ->tag('console.command', ['command' => 'eloquent:migrate']);

    $services->set('wouterj_eloquent.commands.migrate_fresh', MigrateFreshCommand::class)
        ->args([service('wouterj_eloquent.database_manager'), $migrator, $migrationPath, $env])
        ->tag('console.command', ['command' => 'eloquent:migrate:fresh']);

    $services->set('wouterj_eloquent.commands.migrate_install', MigrateInstallCommand::class)
        ->args([service('wouterj_eloquent.migrations.repository')])
        ->tag('console.command', ['command' => 'eloquent:migrate:install']);

    $services->set('wouterj_eloquent.commands.migrate_refresh', MigrateRefreshCommand::class)
        ->args([$migrator, $migrationPath, $env])
        ->tag('console.command', ['command' => 'eloquent:migrate:refresh']);

    $services->set('wouterj_eloquent.commands.migrate_reset', MigrateResetCommand::class)
        ->args([$migrator, $migrationPath, $env])
        ->tag('console.command', ['command' => 'eloquent:migrate:reset']);

    $services->set('wouterj_eloquent.commands.migrate_rollback', MigrateRollbackCommand::class)
        ->args([$migrator, $migrationPath, $env])
        ->tag('console.command', ['command' => 'eloquent:migrate:rollback']);

    $services->set('wouterj_eloquent.commands.migrate_status', MigrateStatusCommand::class)
        ->args([$migrator, $migrationPath, $env])
        ->tag('console.command', ['command' => 'eloquent:migrate:status']);

    $services->set('wouterj_eloquent.commands.seed', SeedCommand::class)
        ->args([service('service_container'), service('wouterj_eloquent.database_manager'), param('kernel.bundles'), $env])
        ->tag('console.command', ['command' => 'eloquent:seed']);
};
