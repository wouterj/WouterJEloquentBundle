# WouterJEloquentBundle

The WouterJEloquentBundle claims to integrate the [Eloquent ORM][eloquent]
into the Symfony2 framework.

[![Build Status](https://travis-ci.org/WouterJ/WouterJEloquentBundle.png?branch=master)](https://travis-ci.org/WouterJ/WouterJEloquentBundle)

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

    wouterj_eloquent:
        driver:   mysql
        host:     localhost
        database: db_name
        username: root
        password: pass
        prefix:   ~

## Documentation

To learn more about the bundle, read the [documentation][docs].

## License

This project is licensed under the MIT license. For more information, see the
[LICENSE][license] file included in this bundle.

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
