Configuration Reference
=======================

Full configuration
------------------

.. code-block:: yaml

    wouterj_eloquent:
        connections:

            # Prototype
            name:
                database:  ~     # the only required option
                driver:    mysql
                host:      localhost
                port:      null
                username:  root
                password:  ''
                charset:   utf8
                collation: utf8_unicode_ci
                prefix:    ''
                sticky:    false
                read:
                    host:      null
                    port:      null
                    database:  null
                    username:  null
                    password:  null
                    charset:   null
                    collation: null
                    prefix:    null
                write:
                    host:      null
                    port:      null
                    database:  null
                    username:  null
                    password:  null
                    charset:   null
                    collation: null
                    prefix:    null
        default_connection: default
        eloquent: false
        aliases: false

Connections
-----------

The ORM accepts multiple connections with a different name. A lot of settings
have defaults, the only required setting is the ``database`` setting.

If you want to configure only one connection, you can pass the connection
setting directly to the root configuration:

.. code-block:: yaml

    # config/packages/eloquent.yaml
    wouterj_eloquent:
        driver: sqlite
        host: local
        database: foo.db
        username: user
        password: pass
        prefix: symfo_
        sticky: true
        read:
          host: ['localhost']
        write:
          host: ['localhost']


This will create a connection called ``default``. If the defaults fits your
needs, the minimal configuration to get started is:

.. code-block:: yaml

    # config/packages/eloquent.yaml
    wouterj_eloquent:
        database: the_database_name

Drivers
~~~~~~~

The Eloquent ORM supports four database drivers:

* mysql
* postgres
* sqlserver
* sqlite

Default Connection
~~~~~~~~~~~~~~~~~~

If your default connection is not ``default``, you can specify its name using
this option.

Eloquent
--------

By default, the Eloquent ORM is disabled. This means you can use the
QueryBuilder, but not the Eloquent models. To activate the Eloquent ORM, you
have to set the ``eloquent`` option to ``true``:

.. code-block:: yaml

    wouterj_eloquent:
        # ...
        eloquent: true

Aliases
-------

The EloquentBundle provides two facades: ``Db`` and ``Schema``. You can also
alias these facades, which means that you can always use ``Db`` and ``Schema``
directly, without including a ``use`` statement.

You can activate both facades to be aliases by setting ``aliases`` to
``true``:

.. code-block:: yaml

    # config/packages/eloquent.yaml
    wouterj_eloquent:
        # ...
        aliases: true

You can also specify either ``Db`` or ``Schema`` to be aliased:

.. code-block:: yaml

    # config/packages/eloquent.yaml
    wouterj_eloquent:
        # ...
        aliases:
            db: true

« `Migrations <migrations.rst>`_ • `Back to the table of contents <../../README.md#table-of-contents>`_ »
