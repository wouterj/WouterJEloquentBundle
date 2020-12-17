# WouterJEloquentBundle

The WouterJEloquentBundle claims to integrate the [Eloquent ORM][eloquent]
into the Symfony framework.

If you wish to use the [Symfony Serializer][serializer] with [Eloquent
Models][eloquent-model] you can check [EloquentSerializer][eloquent-serializer].


## Maintained Releases

Only the latest release of this bundle is maintained. Backwards
compatible support for new versions of PHP, Symfony and Laravel are
considered bug fixes. Dropping support for old versions is considered a
feature.

New releases will be made as much as once a week following semantic
versioning. The default branch (2.x) will be released as minor or patch
version, depending on the changes with the last release (features or
only bug fixes).


## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and run the
following command to download the latest stable version of this bundle:

```bash
$ composer require wouterj/eloquent-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter][composer] of the Composer documentation.

When you want to use useful make commands (e.g. ``make:model``), also
make sure you have the [Symfony MakerBundle][maker-bundle] installed.


### Step 2: Enable the Bundle

If you're using [Symfony Flex][symfony-flex], the previous step already got
you up and running and you can skip this step!

Otherwise, enable the bundle by adding it to the list of registered
bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// config/bundles.php

return [
    // ...
    WouterJ\EloquentBundle\WouterJEloquentBundle::class => ['all' => true],
];
```


### Step 3: Configure the Database

To use the Eloquent ORM, configure a connection by setting the correct
environment variables in `.env.local`:

```ini
# .env.local
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
1. [Usage](docs/usage.rst)
    1. [Query Builder](docs/usage.rst#query-builder)
    1. [Eloquent ORM](docs/usage.rst#eloquent-orm)
    1. [Using Services instead of Facades](docs/usage.rst#using-services-instead-of-facades)
1. [Migrations and Seeding](docs/migrations.rst)
    1. [Running seeders](docs/migrations.rst#running-seeders)
    1. [Setting up](docs/migrations.rst#setting-up)
    1. [Generating migrations](docs/migrations.rst#generating-migrations)
    1. [Running migrations](docs/migrations.rst#running-migrations)
    1. [Rolling migrations](docs/migrations.rst#rolling-back-migrations)
    1. [Refreshing the database](docs/migrations.rst#refreshing-the-database)
1. [Using Models in Forms](docs/forms.rst)
    1. [Binding the Object to the Form](docs/forms.rst#binding-the-object-to-the-form)
    1. [Form Type Guessing](docs/forms.rst#form-type-guessing)
    1. [Form Validation](docs/forms.rst#form-validation)
1. [Events and Observers](docs/events.rst)
    1. [Register Listeners](docs/events.rst#register-listeners)
    1. [Observers](docs/events.rst#observers)
        1. [Observers as Services](docs/events.rst#observers-as-services)
1. [Configuration](docs/configuration.rst)
    1. [Full configuration](docs/configuration.rst#full-configuration)
    1. [Connections](docs/configuration.rst#connections)
        1. [Drivers](docs/configuration.rst#drivers)
        1. [Default connection](docs/configuration.rst#default-connection)
    1. [Eloquent](docs/configuration.rst#eloquent)
    1. [Aliases](docs/configuration.rst#aliases)
1. [License][license]
1. [Contributing](#contributing)
1. [Backwards Compatibility](#backwards-compatibility)


## License

This project is licensed under the MIT license. For more information, see the
[license][license] file included in this bundle.


## Contributing

I love contributors. You can submit fixes, report bugs, share your opinion,
advocate this bundle or just say "hello". I welcome anything that makes this
project better.


[serializer]: http://symfony.com/doc/current/components/serializer.html
[eloquent-model]: https://laravel.com/docs/5.4/eloquent#eloquent-model-conventions
[eloquent-serializer]: https://github.com/theofidry/EloquentSerializer/blob/master/README.md
[maker-bundle]: https://symfony.com/doc/current/bundles/SymfonyMakerBundle/index.html
[eloquent]: http://laravel.com/docs/database
[composer]: https://getcomposer.org/doc/00-intro.md
[symfony-flex]: https://symfony.com/doc/current/setup/flex.html
[license]: LICENSE
