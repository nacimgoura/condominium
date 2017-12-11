<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Conversation;
use App\Entity\User;
use App\Form\ConversationType;

class ForumController extends Controller
{
    /**
     * @Route("/forum", name="forum_index")
     */
    public function index() {

        $listPost = $this->getDoctrine()
            ->getRepository(Conversation::class)
            ->findAllAuthorized($this->getUser());

        return $this->render('forum/index.html.twig', [
            'listPost' => $listPost
        ]);
    }

    /**
     * @Route("/forum/post/{id}", requirements={"id" = "\d+"}, name="forum_index_detail")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail($id, Request $request) {
        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        $post = $this->getDoctrine()
            ->getRepository(Conversation::class)
            ->find($id);

        if ($form->isSubmitted() && $form->isValid()) {

            $message = $form->getData();
            $message->setUser($this->getUser());
            $message->setConversation($post);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
        }

        return $this->render('forum/detail.html.twig', [
            'post' => $post,
            'action' => $this->generateUrl('forum_index_detail', ['id' => $id]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forum/add", name="forum_add_post")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addPost(Request $request) {

        $post = new Conversation();
        $listUser = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllExceptAdminAndUs($this->getUser()->getUsername());

        $post->setAuthorizedUser($listUser);

        $form = $this->createForm(ConversationType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();
            $post->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('forum_index');
        }

        return $this->render('forum/addconversation.html.twig', [
            'action' => $this->generateUrl('forum_add_post'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forum/post/archive/{id}", name="forum_archive_post")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function archivePost($id) {

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Conversation::class)->find($id);

        $post->setIsArchived(true);
        $em->flush();

        $this->addFlash(
            'success',
            'Conversation archivée avec succès!'
        );

        return $this->redirectToRoute('forum_index');
    }

    /**
     * @Route("/forum/post/delete/{id}", name="forum_delete_post")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePost($id) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Conversation::class)->find($id);

        $em->remove($post);
        $em->flush();

        $this->addFlash(
            'success',
            'Conversation supprimé avec succès!'
        );

        return $this->redirectToRoute('forum_index');
    }
}