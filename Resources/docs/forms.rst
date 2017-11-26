Using Models in Forms
=====================

The eloquent models can work perfectly with forms. However, due to
the lazyness of models, you have to do things a little bit different
than documented in the `Symfony documentation`_.

Binding the Object to the Form
------------------------------

Creating forms is done exactly the same as normal (refer to the
`Symfony documentation`_ if you don't know how this is done normally):

.. code-block:: php

    namespace AppBundle\Controller;

    use AppBundle\Model\Post;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\HttpFoundation\Request;

    class PostController extends Controller
    {
        public function createAction(Request $request)
        {
            $post = new Post();

            $form = $this->createFormBuilder($post)
                ->add('title', TextType::class)
                ->add('body', TextareaType::class)
                ->getForm();

            $form->handleRequest($form);

            if ($form->isSubmitted() && $form->isValid()) {
                $post->save();

                return $this->render('post/create_success.html.twig');
            }

            return $this->render('post/create_form.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

Form Type Guessing
~~~~~~~~~~~~~~~~~~

If you use `Eloquent attribute casting`_, the bundle is able to guess your
form types. For instance, assume this model:

.. code-block:: php

    namespace AppBundle\Model;

    use Illuminate\Database\Eloquent\Model;

    class Post extends Model
    {
        public $casts = [
            'title' => 'string',
            'published_at' => 'date',
            'is_published' => 'boolean',
            'body' => 'string',
        ];
    }

Then you can generate your form like this:

.. code-block:: php

    $form = $this->createFormBuilder(new Post())
        ->add('title')
        ->add('published_at')
        ->add('is_published')
        ->add('body', TextareaType::class) // string is always transformed to TextType
        ->getForm();

Form Validation
---------------

As the properties cannot be defined explicitly in the model, validating
them is a bit more difficult. There are a couple solutions here:

#. Don't bind models to your form, but instead `create a new class`_ specifically for your form;
#. Define getters for all your properties and `validate the return values`_.

The second solution would look like this:

.. code-block:: php

    namespace AppBundle\Model;

    use Illuminate\Database\Eloquent\Model;
    use Symfony\Component\Validator\Constraints as Assert;

    class Post extends Model
    {
        public $casts = [
            'title' => 'string',
            'published_at' => 'date',
            'is_published' => 'boolean',
            'body' => 'string',
        ];

        /**
         * @Assert\NotBlank
         * @Assert\Length(max=128)
         */
        public function getTitle()
        {
            return $this->title;
        }

        /** @Assert\NotBlank */
        public function getBody()
        {
            return $this->body;
        }
    }

« `Migrations and Seeding <migrations.rst>`_ • `Events and Observers <events.rst>`_ »

.. _Symfony documentation: https://symfony.com/doc/current/forms
.. _Eloquent attribute casting: https://laravel.com/docs/eloquent-mutators#attribute-casting
.. _create a new class: https://stovepipe.systems/post/avoiding-entities-in-forms
.. _validate the return values: https://symfony.com/doc/current/validation#getters
