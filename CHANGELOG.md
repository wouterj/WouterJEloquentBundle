Changelog
=========

2.4.0
-----

 * Added Laravel 10 support
 * Dropped Symfony <5.4 support

2.0.0
-----

 * Added Laravel ^7.15 and ^8.12 support
 * Added PHP 8 support
 * Added support for Read & Write Connections
 * Added schema support for PostgreSQL
 * Deleted second argument of `ServiceContainerDispatcher`
 * Transformed the `make:*` commands to use the MakerBundle. Make sure
   `symfony/maker-bundle` is installed if you want to use the `make:*`
   commands
 * Removed the `--target` option of `make:seeder`, the location is now
   auto-discovered based on the classname and autoloading configuration

1.2.0
-----

 * Added Laravel ^6.18 support
 * Dropped Laravel <6 support
 * Dropped Symfony <4.4 support
 * Dropped PHP <7.2 support

1.1.0
-----

 * Added Laravel 5.6, 5.7 and 5.8 support
 * Dropped Symfony 2.8 support
 * Dropped PHP 7.0 support

1.0.2
-----

 * Added `Schema::connection()` to allow multiple connections

1.0.1
-----

 * Added support for env variable usage in connection settings

1.0.0
-----

 * Added Symfony 4.0 support
 * Added form type guesser
 * Added Var Dumper caster for Eloquent models
 * Added the `eloquent:make:seeder` command
 * Added the `eloquent:migrate:fresh` command
 * Added discovery of `App\Seed\DatabaseSeeder` when using Symfony Flex
 * Added Laravel 5.5 support

0.5.0
-----

 * Fixed not creating the migrations directory on `eloquent:migrate:make`
 * Added Data collector
 * Added Laravel 5.4 support

0.4.0
-----

 * Added `@internal` and `@final` PHPdoc annotations
 * Added model events integration
 * Deprecated `postgres` and `sql server` driver names in favor of `pgsql` and
   `sqlsrv`. The old names will be unsupported as of 1.0.

0.3.0
-----

 * Added `wouterj_eloquent.migration_path` parameter to store the migration path
 * Added incompatibility for Laravel 5.3+ packages ([37aceab](https://github.com/wouterj/WouterJEloquentBundle/commit/37aceab0ede2af755b96c7d7356b8698a8efcca2) - theofidry)
 * Refactored booting of eloquent and facades to use `Bundle#boot()` ([669e9f4](https://github.com/wouterj/WouterJEloquentBundle/commit/669e9f4e05a6d6179e046c80af5e606878d413ce) - wouterj)
 * Required at least one connection ([7228be3](https://github.com/wouterj/WouterJEloquentBundle/commit/7228be36e1827bc75690c970a3390e1b32f69467) - wouterj)
