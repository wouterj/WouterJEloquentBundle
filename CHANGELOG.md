Changelog
=========

 * Added model events integration
 * Deprecated `postgres` and `sql server` driver names in favor of `pgsql` and
   `sqlsrv`. The old names will be unsupported as of 1.0.

0.3.0
-----

 * Added `wouterj_eloquent.migration_path` parameter to store the migration path
 * Added incompatibility for Laravel 5.3+ packages ([37aceab](https://github.com/wouterj/WouterJEloquentBundle/commit/37aceab0ede2af755b96c7d7356b8698a8efcca2) - theofidry)
 * Refactored booting of eloquent and facades to use `Bundle#boot()` ([669e9f4](https://github.com/wouterj/WouterJEloquentBundle/commit/669e9f4e05a6d6179e046c80af5e606878d413ce) - wouterj)
 * Required at least one connection ([7228be3](https://github.com/wouterj/WouterJEloquentBundle/commit/7228be36e1827bc75690c970a3390e1b32f69467) - wouterj)
