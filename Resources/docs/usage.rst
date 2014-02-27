Usage
-----

Using this bundle is almost equal to how you use the Eloquent ORM in
laravel_.

Query Builder
-------------

You can use the `Query Builder`_:

.. code-block:: php

    namespace Acme\DemoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class DemoController extends Controller
    {
        public function indexAction()
        {
            $posts = DB::table('posts')->where('created_at', '>', '...')->get();

            return $this->render('AcmeDemoBundle:Demo:posts.html.twig', array(
                'posts' => $posts,
            ));
        }
    }

.. caution::

    Don't forget to enable the ``DB`` aliases if you want to use the ``DB``
    class directly. Otherwise, you have to include the
    ``WouterJ\EloquentBundle\Facade\DB``.

Eloquent ORM
------------

If you enabled the eloquent ORM, you can also use the `Eloquent models`_. First
create a model:

.. code-block:: php

    namespace Acme\DemoBundle\Model\Post;

    use Illuminate\Database\Eloquent\Model;

    class Post extends Model
    {
    }

Now, you can persist, fetch, delete and update your models where you like:

.. code-block:: php

    namespace Acme\DemoBundle\Controller;

    use Acme\DemoBundle\Model\Post;
    // ...

    public function indexAction()
    {
        $post = Post::find(1);

        return $this->render('AcmeDemoBundle:Demo:post.html.twig', array(
            'post' => $post,
        ));
    }

Using Services instead of Facades
---------------------------------

You may prefer to use services instead of the magic Facades. The bundle
provides 2 usefull services:

* ``wouterj_eloquent`` - This is the ``Capsule`` class, it can handle all core
  methods of the ``Schema`` and ``DB`` facades;
* ``wouterj_eloquent.database_manager`` - This service is equal to the ``DB``
  facade.

.. _laravel: http://laravel.com/docs/database
.. _`Query Builder`: http://laravel.com/docs/queries
.. _`Eloquent models`: http://laravel.com/docs/eloquent
