<?php

namespace AppBundle\Controller;

use AppBundle\Model\CastingUser;
use Twig\Environment;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormController
{
    public function create(Request $request, FormFactoryInterface $formFactory, Environment $twig)
    {
        $user = new CastingUser();
        $form = $formFactory->createBuilder(FormType::class, $user)
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

        return new Response($twig->render('form/create.twig', ['form' => $form->createView()]));
    }
}
