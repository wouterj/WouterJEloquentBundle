<?php

namespace AppBundle\Controller;

use AppBundle\Model\CastingUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (class_exists('Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController')) {
    class ParentFormController extends AbstractController {}
} else {
    class ParentFormController extends Controller {}
}

class FormController extends ParentFormController
{
    public function create(Request $request)
    {
        $user = new CastingUser();
        $form = $this->createFormBuilder($user)
            ->add('name')
            ->add('password')
            ->add('date_of_birth')
            ->add('is_admin')
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->save();

            return new Response('Successfully created user!');
        }

        return $this->render('form/create.twig', ['form' => $form->createView()]);
    }
}
