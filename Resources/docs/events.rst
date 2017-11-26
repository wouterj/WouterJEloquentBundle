Events
======

Eloquent models fire events during their lifecycle. You can hook into these
events in order to execute code at specific moments in the lifecycle. The fired
events are: creating, created, updating, updated, saving, saved, deleting,
deleted, restoring and restored.

For more information about Eloquent events, checkout the `events section`_ of
the Laravel documentation.

Register Listeners
------------------

In order to hook into such an event, register a listener for the model. You can
do this e.g. in the ``Bundle::boot()`` method:

.. code-block:: php

    // ...
    use AppBundle\Model\User;

    class AppBundle extends Bundle
    {
        public function boot()
        {
            User::created(function (User $user) {
                // ... build a party, a new user is born!
            });
        }
    }

Observers
---------

If you're listening to multiple events, it's often easier to create observers.
This way, all hooks are grouped in classes.

.. code-block:: php

    namespace AppBundle\Observer\UserObserver;

    use AppBundle\Model\User;

    class UserObserver
    {
        public function created(User $user)
        {
            // ... build that party again
        }

        public function deleting(User $user)
        {
            // ... convince them to cancel the operation!
        }
    }

The observers can again be bound to the model in ``Bundle::boot()``:

.. code-block:: php

    // ...
    use AppBundle\Observer\UserObserver;

    class AppBundle extends Bundle
    {
        public function boot()
        {
            User::observe(UserObserver::class);
        }
    }

Observers as Services
~~~~~~~~~~~~~~~~~~~~~

Often, your observers need access to some services. In this case, make sure
your tag the service with ``wouterj_eloquent.observer``:

.. code-block:: yaml

    # app/config/services.yml
    services:
        # ...

        app.user_observer:
            class: AppBundle\Observer\UserObserver
            arguments: ['@logger'] # for instance
            tags:
                - { name: wouterj_eloquent.observer }

Then, pass the service ID in the ``Bundle::boot()`` method instead:

.. code-block:: php

    // ...
    class AppBundle extends Bundle
    {
        public function boot()
        {
            User::observer('app.user_observer');

            // or when using a listener
            User::creating('app.user_observer@beforeCreation');
        }
    }

« `Using Models in Forms <forms.rst>`_ • `Configuration <configuration.rst>`_ »

.. _events section: https://laravel.com/docs/eloquent#events
