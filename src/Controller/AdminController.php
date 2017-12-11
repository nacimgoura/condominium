<?php

namespace App\Controller;

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

        $user->setRoles(['ROLE_USER']);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $user->setPassword($encoder->encodePassword($user, $user->getFirstname()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

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
}