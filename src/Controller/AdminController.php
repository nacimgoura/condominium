<?php

namespace App\Controller;

use App\Entity\Condominium;
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
     * @return Response
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder) {

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

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
                $oldUser = $doctrine
                    ->getRepository(User::class)
                    ->findOneById($condominium->getManager()->getId());

                $oldUser->setRoles(['ROLE_USER']);
                $condominium->setManager($user);

                $em->persist($condominium);
                $em->persist($oldUser);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Utilisateur ajouté avec succès!'
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
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editUser($id, Request $request){

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $form = $this->createForm(UserType::class, $user);

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
            'action' => $this->generateUrl('admin_edit_user', ['id' => $id]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", requirements={"id" = "\d+"}, name="admin_delete_user")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser($id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

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
     * @Route("/admin/condominium", name="admin_gestion_condominium")
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
     * @Route("/admin/condominium/delete/{id}", name="admin_delete_condominium")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCondominium($id) {
        $user = $this->getDoctrine()
            ->getRepository(Condominium::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'success',
            'Copropriété supprimée avec succès!'
        );

        return $this->redirectToRoute('admin_gestion_condominium');
    }
}