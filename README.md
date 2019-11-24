# WouterJEloquentBundle

The WouterJEloquentBundle claims to integrate the [Eloquent ORM][eloquent]
into the Symfony framework.

If you wish to use the [Symfony Serializer][serializer] with [Eloquent Models][eloquent-model] you can check [EloquentSerializer][eloquent-serializer].

[![Build Status](https://travis-ci.org/wouterj/WouterJEloquentBundle.svg?branch=master)](https://travis-ci.org/wouterj/WouterJEloquentBundle)


## Supported versions

|                 | Laravel 5.5 | Laravel 5.x | Laravel 6.x |
| --------------- | ----------- | ----------- | ----------- |
| **Symfony 2.x** | 1.0.x       | 1.x         | -           |
| **Symfony 3.x** | 1.0.x       | 1.x         | 2.x         |
| **Symfony 4.x** | 1.0.x       | 1.x         | 2.x         |

[Contribute to this repository](#contributing) to this repository if you want
to add support for other versions.


## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require wouterj/eloquent-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter][composer] of the Composer documentation.


### Step 2: Enable the Bundle

If you're using [Symfony Flex][symfony-flex], the previous step already got
you up and running and you can skip this step! Otherwise, enable the bundle
by adding it to the list of registered bundles in the `app/AppKernel.php`
file of your project:

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

To use the Eloquent ORM, configure a connection by setting the correct environment
variables in `.env`:

```ini
# .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=symfony
DB_USERNAME=root
DB_PASSWORD=s3cr3t
```

If you're not using [Symfony Flex][symfony-flex], create the configuration file
yourself:

```yaml
# config/packages/eloquent.yaml
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
1. [Usage](Resources/docs/usage.rst)
    1. [Query Builder](Resources/docs/usage.rst#query-builder)
    1. [Eloquent ORM](Resources/docs/usage.rst#eloquent-orm)
    1. [Using Services instead of Facades](Resources/docs/usage.rst#using-services-instead-of-facades)
1. [Migrations and Seeding](Resources/docs/migrations.rst)
    1. [Running seeders](Resources/docs/migrations.rst#running-seeders)
    1. [Setting up](Resources/docs/migrations.rst#setting-up)
    1. [Generating migrations](Resources/docs/migrations.rst#generating-migrations)
    1. [Running migrations](Resources/docs/migrations.rst#running-migrations)
    1. [Rolling migrations](Resources/docs/migrations.rst#rolling-back-migrations)
    1. [Refreshing the database](Resources/docs/migrations.rst#refreshing-the-database)
1. [Using Models in Forms](Resources/docs/forms.rst)
    1. [Binding the Object to the Form](Resources/docs/forms.rst#binding-the-object-to-the-form)
    1. [Form Type Guessing](Resources/docs/forms.rst#form-type-guessing)
    1. [Form Validation](Resources/docs/forms.rst#form-validation)
1. [Events and Observers](Resources/docs/events.rst)
    1. [Register Listeners](Resources/docs/events.rst#register-listeners)
    1. [Observers](Resources/docs/events.rst#observers)
        1. [Observers as Services](Resources/docs/events.rst#observers-as-services)
1. [Configuration](Resources/docs/configuration.rst)
    1. [Full configuration](Resources/docs/configuration.rst#full-configuration)
    1. [Connections](Resources/docs/configuration.rst#connections)
        1. [Drivers](Resources/docs/configuration.rst#drivers)
        1. [Default connection](Resources/docs/configuration.rst#default-connection)
    1. [Eloquent](Resources/docs/configuration.rst#eloquent)
    1. [Aliases](Resources/docs/configuration.rst#aliases)
1. [License][LICENSE]
1. [Contributing](#contributing)
1. [Backwards Compatibility](#backwards-compatibility)


## License

This project is licensed under the MIT license. For more information, see the
[license][license] file included in this bundle.


## Contributing

I love contributors. You can submit fixes, report bugs, share your opinion,
advocate this bundle or just say "hello". I welcome anything that makes this
project better.


## Backwards Compatibility

This bundle follows SemVer, meaning that no minor (`1.x`) release will contain
BC breaks. A new major version is released as soon as BC breaks are introduced.
These will be explained in detail in the `UPGRADE-*.md` file shipped with the
source code.

Classes or methods with the `@internal` PHPdoc annotation are not meant to use
or extend.  Backwards compatibility is not guaranteed. Classes or methods with
the `@final` PHPdoc annotation are only meant for usage. Backwards
compatibility when extending these classes is not guaranteed.


[serializer]: http://symfony.com/doc/current/components/serializer.html
[eloquent-model]: https://laravel.com/docs/5.4/eloquent#eloquent-model-conventions
[eloquent-serializer]: https://github.com/theofidry/EloquentSerializer/blob/master/README.md
[eloquent]: http://laravel.com/docs/database
[composer]: https://getcomposer.org/doc/00-intro.md
[symfony-flex]: https://symfony.com/doc/current/setup/flex.html
[docs]: Resources/docs/index.rst
[license]: LICENSE
[cs]: http://symfony.com/doc/current/contributing/code/standards.html
