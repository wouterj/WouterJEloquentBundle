Using the Eloquent User Provider
================================

The bundle allows you to use Eloquent models as users in the `Symfony
SecurityBundle`_. For this, you have to create a user model and configure
the user provider.

Creating the User Model
-----------------------

First, create a `User` model (`php bin/console make:model User`). This
model has to implement the `Symfony\Component\Security\Core\User\UserInterface`:

.. code-block:: php

    namespace App\Model;

    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

    class User extends Model implements UserInterface, PasswordAuthenticatedUserInterface
    {
        public $fillable = ['email', 'password', 'roles'];

        public function getRoles()
        {
            return $this->roles;
        }

        public function getPassword(): ?string
        {
            return $this->password;
        }

        public function getSalt()
        {
            return null;
        }

        public function getUsername()
        {
            return $this->email;
        }

        public function getUserIdentifier(): string
        {
            return $this->email;
        }

        public function eraseCredentials()
        {
        }
    }

Also make sure you've written a migration for this model.

Configuring the User Provider
-----------------------------

After you have a working Eloquent User model, you have to configure Symfony
security to use this model through a user provider:

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        # ...

        providers:
            app_user_provider:
                eloquent:
                    model: 'App\Model\User'
                    attribute: email

The eloquent user provider has 2 required configuration options:

`model`
    The fully qualified class name of your user model
`attribute`
    The name of the attribute representing the username/user identifier

« `Using Models in Forms <forms.rst>`_ • `Events and Observers <events.rst>`_ »

.. _Symfony SecurityBundle: https://symfony.com/doc/current/security
