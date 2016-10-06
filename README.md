# WouterJEloquentBundle

The WouterJEloquentBundle claims to integrate the [Eloquent ORM][eloquent]
into the Symfony2 framework.

[![Build Status](https://travis-ci.org/wouterj/WouterJEloquentBundle.svg?branch=master)](https://travis-ci.org/wouterj/WouterJEloquentBundle)


## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require wouterj/eloquent-bundle "^0.2"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter][composer] of the Composer documentation.


### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new WouterJ\EloquentBundle\WouterJEloquentBundle(),
        );

        // ...
    }

    // ...
}
```


### Step 3: Configure the Database

To use the Eloquent ORM and its database features, you need to configure the
bundle with the database information:

```yaml
wouterj_eloquent:
    driver:   mysql
    host:     localhost
    database: db_name
    username: root
    password: pass
    prefix:   ~
```

For more information, refer to [the documentation](#table-of-content) below.


## Table of Content

1. [Installation](#installation)
    1. [Step 1: Download the Bundle](#step-1-download-the-bundle)
    1. [Step 2: Enable the Bundle](#step-2-enable-the-bundle)
    1. [Step 3: Configure the Database](#step-3-configure-the-database)
1. [Usage](resources/docs/usage.rst)
    1. [Query Builder](resources/docs/usage.rst#query-builder)
    1. [Eloquent ORM](resources/docs/usage.rst#eloquent-orm)
    1. [Using Services instead of Facades](resources/docs/usage.rst#using-services-instead-of-facades)
1. [Migrations](resources/docs/migrations.rst)
    1. [Running seeders](resources/docs/migrations.rst#running-seeders)
    1. [Setting up](resources/docs/migrations.rst#setting-up)
    1. [Generating migrations](resources/docs/migrations.rst#generating-migrations)
    1. [Running migrations](resources/docs/migrations.rst#running-migrations)
    1. [Rolling migrations](resources/docs/migrations.rst#rolling-back-migrations)
    1. [Refreshing the database](resources/docs/migrations.rst#refreshing-the-database)
1. [Configuration](resources/docs/configuration.rst)
    1. [Full configuration](resources/docs/configuration.rst#full-configuration)
    1. [Connections](resources/docs/configuration.rst#connections)
        1. [Drivers](resources/docs/configuration.rst#drivers)
        1. [Default connection](resources/docs/configuration.rst#default-connection)
    1. [Eloquent](resources/docs/configuration.rst#eloquent)
    1. [Aliases](resources/docs/configuration.rst#aliases)
    1. [Other configuration formats](resources/docs/configuration.rst#other-configuration-formats)
        1. [XML](resources/docs/configuration.rst#xml)
        1. [PHP](resources/docs/configuration.rst#php)
1. [License](#license)
1. [Contributing](#contributing)
1. [Roadmap](#roadmap)


## License

This project is licensed under the MIT license. For more information, see the
[license][license] file included in this bundle.


## Contributing

I love contributors. You can submit fixes, report bugs, share your opinion,
advocate this bundle or just say "hello". I welcome anything that makes this
project better.


## Roadmap

To view the roadmap to a full featured Eloquent bundle, see the
[roadmap][roadmap].


[eloquent]: http://laravel.com/docs/database
[composer]: https://getcomposer.org/doc/00-intro.md
[docs]: resources/docs/index.rst
[license]: LICENSE
[cs]: http://symfony.com/doc/current/contributing/code/standards.html
[roadmap]: resources/docs/roadmap.rst
