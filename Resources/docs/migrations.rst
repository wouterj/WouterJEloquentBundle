Migrations and Seeding
======================

Migrations are the way to create, modify and share your database
schema. This makes it easy to have the same schema on all machines
running the application. Seeders will fill your database with test
data.

If you haven't done it already, read the Laravel documentation about
`migrations`_ and `seeding`_ for the full documentation.

Running Seeders
---------------

.. code-block:: bash

    $ php app/console eloquent:seed

This will run the ``DatabaseSeeder`` classes which live in the ``Seed``
namespace of all registered bundles. You can also specify a specific
seeder class:

.. code-block:: bash

    $ php app/console eloquent:seed AppBundle\Seed\FlightsTableSeeder

The ``--database=<NAME>`` option allows you to use another connection
than the default one.

Setting Up
----------

Before you can use migrations, you have to configure your environment
a little bit. First, you need to create the ``migrations`` table that
will be used to keep track of the migrations. This is done by executing
the ``eloquent:migrate:install`` command:

.. code-block:: bash

    $ php bin/console eloquent:migrate:install

Secondly, you have to configure the Composer autoload classmap for the
migrations directory by updating your ``composer.json`` file:

.. code-block:: json

    {
        "...": "...",
        "autoload": {
            "classmap": ["app/migrations"]
        }
    }

Generating Migrations
---------------------

.. code-block:: bash

    $ php bin/console eloquent:migrate:make create_flights_table

This will generate a migration file in ``app/migrations``.

By specifying the ``--create`` and ``-table`` options, the generator
will also generate some bootstrap code for you. The ``--table`` option
can be used to specify the table name to update and ``--create`` to
specify which table will be created by the migrations:

.. code-block:: bash

    $ php bin/console eloquent:migrate:make --create=flights create_flights_table
    $ php bin/console eloquent:migrate:make --table=flights add_aircraft_to_flights_table

======================  ==========================================================================================================
Option                  Description
======================  ==========================================================================================================
``--database=<NAME>``   The connection to use.
``--path=<PATH>``       The location where the migration file should be created, defaults to the main migration path.
``--table=<NAME>``      The name of a table that is updated during the migration.
``--create[=<TABLE>]``  Indicates that the migration will create a table. The value is a shortcut for ``--create --table=<NAME>``.
======================  ==========================================================================================================

Running Migrations
------------------

.. code-block:: bash

    $ php bin/console eloquent:migrate

Use the ``--force`` option to suppress the confirmation question when running
this command in production. Other options are:

====================   =======================================================================
Option                 Description
====================   =======================================================================
``--database=<NAME>``  The connection to use.
``--path=<PATH>``      The path to the migrations files (in case it's not ``app/migrations``).
``--step``             Run the migrations one by one so they can be rolled back individually.
``--force``            Suppress the confirmation question when executing this in production.
``--pretend``          Do not run the migrations, only dump the SQL queries that would be run.
``--seed``             To automatically seed the database after running the migrations.
====================   =======================================================================

Rolling Back Migrations
-----------------------

.. code-block:: bash

    $ php bin/console eloquent:migrate:rollback

This commands rolls back the last executed batch of migrations. To rollback
*all* migrations, use ``eloquent:migrate:reset``.

====================   ==============================================================================
Option                 Description
====================   ==============================================================================
``--database=<NAME>``  The connection to use.
``--step=<STEP>``      The number of migration batches to be reverted, defaults to only the last one.
``--force``            Suppress the confirmation question when executing this in production.
``--pretend``          Do not run the migrations, only dump the SQL queries that would be run.
====================   ==============================================================================

Refreshing the Database
-----------------------

.. code-block:: bash

    $ php bin/console eloquent:migrate:refresh

This is a shortcut for running ``eloquent:migrate:reset``,
``eloquent:migrate`` and ``eloquent:seed``.

=====================  ===============================================================================
Option                 Description
=====================  ===============================================================================
``--database=<NAME>``  The connection to use.
``--step=<STEP>``      The number of migration batches to be refreshed, defaults to only the last one.
``--path=<PATH>``      The path to the migrations files (in case it's not ``app/migrations``).
``--force``            Suppress the confirmation question when executing this in production.
``--pretend``          Do not run the migrations, only dump the SQL queries that would be run.
``--seed``             To automatically seed the database after running the migrations.
``--seeder``           The class name of the seeder.
=====================  ===============================================================================

How to Configure Migration Paths in a Bundle
--------------------------------------------

If you share a bundle in multiple application, the migration files would not
live in ``app/migrations`` but in your bundle. To make the migrator aware of
this migration directory, call the ``MigrationPathsPass::add()`` method in your
bundle's `extension`_:

.. code-block:: php

    // ...
    use WouterJ\EloquentBundle\DependencyInjection\Compiler\MigrationPathsPass;

    class YourExtension extends Extension
    {
        public function load(array $configs, ContainerBuilder $container)
        {
            // adds the /Resources/migrations directory as migration path
            MigrationPathsPass::add(__DIR__.'/../Resources/migrations');

            // ...
        }
    }

« `Usage <usage.rst>`_ • `Events and Observers <events.rst>`_ »

 .. _migrations: https://laravel.com/docs/migrations
 .. _seeding: https://laravel.com/docs/seeding
 .. _bundles extension: https://symfony.com/doc/current/bundles/extension
