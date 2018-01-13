<?php

namespace App\Controller;

use App\Entity\Condominium;
use App\Form\CondominiumType;
use App\Service\EmailService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function index() {
        return $this->redirectToRoute('admin_gestion_user');
    }

    /**
     * @Route("/admin/user", name="admin_gestion_user")
     */
    public function gestionUser() {
        $listUser = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllExceptAdmin();

        return $this->render('admin/user.html.twig', [
            'listUser' => $listUser
        ]);
    }

    /**
     * @Route("/admin/user/add", name="admin_add_user")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EmailService $emailService
     * @return Response
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder, EmailService $emailService) {

        $user = new User();

        $form = $this->createForm(UserType::class, $user, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();

            $user->setPassword($encoder->encodePassword($user, $user->getFirstname()));

            // si on met le nouvelle utilisateur manager, on enleve son id et son role
            if (in_array('ROLE_MANAGER', $user->getRoles())) {
                $condominium =  $doctrine
                    ->getRepository(Condominium::class)
                    ->findOneById($user->getCondominium()->getId());

                if ($condominium->getManager()) {
                    $oldUser = $doctrine
                        ->getRepository(User::class)
                        ->findOneById($condominium->getManager()->getId());
                    $oldUser->setRoles(['ROLE_USER']);
                    $em->persist($oldUser);
                }

                $condominium->setManager($user);
                $em->persist($condominium);

            }

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Utilisateur ajouté avec succès!'
            );

            $emailService->sendEmail($user->getEmail(),
                'Activation du compte',
                'Cliquer sur ce lien pour activer le compte',
                null,
                $user->getToken()
            );

            return $this->redirectToRoute('admin_gestion_user');
        }

        return $this->render('admin/adduser.html.twig', [
            'action' => $this->generateUrl('admin_add_user'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}", requirements={"id" = "\d+"}, name="admin_edit_user")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function editUser(User $user, Request $request){

        $form = $this->createForm(UserType::class, $user, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->merge($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Utilisateur édité avec succès!'
            );

            return $this->redirectToRoute('admin_gestion_user');
        }

        return $this->render('admin/adduser.html.twig', [
            'action' => $this->generateUrl('admin_edit_user', ['id' => $user->getId()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", requirements={"id" = "\d+"}, name="admin_delete_user")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'success',
            'Utilisateur supprimé avec succès!'
        );

        return $this->redirectToRoute('admin_gestion_user');
    }

    /**
     * @Route("/admin/condominium", name="admin_list_condominium")
     */
    public function showListCondominium() {
        $listCondominium = $this->getDoctrine()
            ->getRepository(Condominium::class)
            ->findAll();

        return $this->render('admin/condominium.html.twig', [
            'listCondominium' => $listCondominium
        ]);
    }

    /**
     * @Route("/admin/condominium/add", name="admin_add_condominium")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addCondominium(Request $request) {

        $condominium = new Condominium();

        $form = $this->createForm(CondominiumType::class, $condominium);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $condominium = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($condominium);
            $em->flush();

            $this->addFlash(
                'success',
                'Copropriété créé avec succès!'
            );

            return $this->redirectToRoute('admin_list_condominium');
        }

        return $this->render('admin/addcondominium.html.twig', [
            'action' => $this->generateUrl('admin_add_condominium'),
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/condominium/delete/{id}", name="admin_delete_condominium")
     * @param Condominium $condominium
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCondominium(Condominium $condominium) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($condominium);
        $em->flush();

        $this->addFlash(
            'success',
            'Copropriété supprimée avec succès!'
        );

        return $this->redirectToRoute('admin_gestion_condominium');
    }
}