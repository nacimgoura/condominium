<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils){

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/account/{token}", name="enabled_account")
     * @param $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function enableAccount($token, Request $request, UserPasswordEncoderInterface $encoder, NotificationService $notificationService) {

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneByToken($token);

        $form = $this->createForm(UserType::class, $user, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $user->setIsEnabled(true);
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $user->setToken(null);

            $em = $this->getDoctrine()->getManager();
            $em->merge($user);
            $em->flush();

            $notificationService->add('User', 'Compte activé.', $user);

            return $this->redirectToRoute('admin_gestion_user');
        }

        return $this->render('login/account.html.twig', [
            'form' => $form->createView(),
            'action' => $this->generateUrl('enabled_account', ['token' => $token])
        ]);
    }
}