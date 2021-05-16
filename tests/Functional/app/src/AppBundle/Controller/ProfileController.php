<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController
{
    public function index(UserInterface $user)
    {
        return new Response('Name: '.$user->name);
    }
}
