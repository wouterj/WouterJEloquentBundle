Usage
=====

Using this bundle is almost equal to how you `use the Eloquent ORM in Laravel`_.

Query Builder
-------------

You can use the `Query Builder`_:

.. code-block:: php

    // src/Controller/PostController.php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use WouterJ\EloquentBundle\Facade\Db;

    class PostController extends AbstractController
    {
        public function indexAction(): Response
        {
            $posts = Db::table('posts')->where('created_at', '>', '...')->get();

            return $this->render('post/list.html.twig', [
                'posts' => $posts,
            ]);
        }
    }

Eloquent ORM
------------

If you enabled the Eloquent ORM, you can also use the `Eloquent models`_. You can
enable Eloquent in the configuration:

.. code-block:: yaml

    # config/packages/eloquent.yaml
    wouter_eloquent:
        eloquent: ~

Then you can create a model:

.. code-block:: bash

    $ php bin/console make:model Post

Which generates a file like this:

.. code-block:: php

    // src/Model/Post.php
    namespace App\Model\Post;

    use Illuminate\Database\Eloquent\Model;

    class Post extends Model
    {
    }

Now, you can persist, fetch, delete and update your models where you like:

.. code-block:: php

    // src/Controller/PostController.php
    namespace App\Controller;

    use AppBundle\Model\Post;
    // ...

    class PostController extends AbstractController
    {
        // ...

        /** @Route("/{postId}", name="read_post")
        public function readAction(int $postId)
        {
            $post = Post::find($postId);

            return $this->render('post/single.html.twig', [
                'post' => $post,
            ]);
        }
    }

Using Services instead of Facades
---------------------------------

You may prefer to use services instead of the magic Facades. The bundle
provides two useful services:

* ``wouterj_eloquent`` - This is the ``Capsule`` class, it can handle all core
  methods of the ``Schema`` and ``Db`` facades;
* ``wouterj_eloquent.database_manager`` - This service is equal to the ``Db``
  facade.

.. _use the Eloquent ORM in laravel: http://laravel.com/docs/database
.. _Query Builder: http://laravel.com/docs/queries
.. _Eloquent models: http://laravel.com/docs/eloquent

« `Installation <../../README.md#installation>`_ • `Migrations <migrations.rst>`_ »
