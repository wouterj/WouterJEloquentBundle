# WouterJEloquentBundle

The WouterJEloquentBundle claims to integrate the [Eloquent ORM][eloquent]
into the Symfony2 framework.

## Installation

The recommend way to install the bundle is using [Composer][composer]. Since
this bundle uses the PSR-4 autoloading, be sure to **always update Composer**
to the latest version before installing the bundle:

    $ php composer.phar require wouterj/eloquent-bundle 0.1.*

After the bundle and the Eloquent ORM are installed, register the bundle in
your kernel:

    // app/AppKernel.php

    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Wj\EloquentBundle\WjEloquentBundle(),
        );

        // ...
        return $bundles;
    }

### Configuration

To use the Eloquent ORM and its database features, you need to configure the
bundle with the database information:

    wouterj_eloquent:
        driver: mysql
        host: localhost
        database: db_name
        username: root
        password: pass
        prefix: ''

## Documentation

To learn more about the bundle, read the [documentation][docs].

## License

This project is licensed under the MIT license. For more information, see the
[LICENSE][license] file included in this bundle.

## Contributing

I love contributors. You can submit fixes, report bugs, give your opinion,
advocate this bundle or just say "hello" to me. Feel free to do anything you
want, as long as you stick to the [Symfony Coding Standards][cs].

> Discussions about the CS used or PRs adding PHPdoc comments have a high risk
> to be rejected.

## Roadmap

To view the roadmap to a full featured Eloquent bundle, see the
[roadmap][roadmap]. 

 [eloquent]: http://laravel.com/docs/database
 [composer]: https://getcomposer.org/
 [docs]: Resources/docs/index.rst
 [license]: Resources/meta/LICENSE
 [cs]: http://symfony.com/doc/current/contributing/code/standards.html
 [roadmap]: Resources/docs/roadmap.rst
