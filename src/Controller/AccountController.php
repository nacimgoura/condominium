<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    /**
     * @Route("/account", name="account_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request) {

        $form = $this->createForm(UserType::class, $this->getUser(), ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->merge($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Donnée utilisateur modifié avec succès!'
            );

            return $this->redirectToRoute('home_index');
        }

        dump($this->getUser());

        return $this->render('account/index.html.twig', [
            'action' => $this->generateUrl('account_index', ['id' => $this->getUser()->getId()]),
            'form' => $form->createView()
        ]);
    }
}