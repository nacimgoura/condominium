<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Project;
use App\Form\MessageType;
use App\Service\NotificationService;
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
     * @Route("/forum/archived", name="forum_show_archived")
     */
    public function showArchived() {

        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $listPost = $this->getDoctrine()
            ->getRepository(Conversation::class)
            ->findBy([
                'isArchived' => true
            ]);

        return $this->render('forum/list_archived.html.twig', [
            'listPost' => $listPost
        ]);
    }

    /**
     * @Route("/forum/post/{id}", requirements={"id" = "\d+"}, name="forum_index_detail")
     * @param Conversation $post
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail(Conversation $post, Request $request) {

        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

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
            'action' => $this->generateUrl('forum_index_detail', ['id' => $post->getId()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forum/add", name="forum_add_post")
     * @param Request $request
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addPost(Request $request, NotificationService $notificationService) {

        $idProject = $request->get('project');

        $post = new Conversation();

        // le post sera visible par le créateur et ceux qu'il autorise
        $condominium = $this->getUser()->getCondominium();
        if ($condominium) {
            $post->setAuthorizedUser($condominium->getUser());
            $post->setCondominium($condominium);
        }

        $form = $this->createForm(ConversationType::class, $post, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();
            $post->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();

            if ($idProject) {
                $project = $this->getDoctrine()
                    ->getRepository(Project::class)
                    ->find($idProject);
                $post->setAuthorizedUser($project->getAuthorizedUser());
                $project->setConversation($post);
                $em->persist($project);
            }

            $em->persist($post);
            $em->flush();

            foreach($post->getAuthorizedUser() as $user) {
                $notificationService->add('Conversation', 'Vous avez été ajouté à la conversation sur le sujet "'.$post->getTitle().'".', $user);
            }

            $this->addFlash(
                'success',
                'Conversation ajouté avec succès!'
            );

            return $this->redirectToRoute('forum_index');
        }

        return $this->render('forum/form.html.twig', [
            'action' => $this->generateUrl('forum_add_post', ['project' => $idProject]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forum/edit/{id}", name="forum_edit_post")
     * @param Conversation $post
     * @param Request $request
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editPost(Conversation $post, Request $request, NotificationService $notificationService) {

        $form = $this->createForm(ConversationType::class, $post, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();
            $post->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();

            $em->merge($post);
            $em->flush();

            foreach($post->getAuthorizedUser() as $user) {
                $notificationService->add('Conversation', 'La conversation "'.$post->getTitle().'" à été modifiée.', $user);
            }

            $this->addFlash(
                'success',
                'Conversation modifié avec succès!'
            );

            return $this->redirectToRoute('forum_index');
        }

        return $this->render('forum/form.html.twig', [
            'action' => $this->generateUrl('forum_edit_post', ['id' => $post->getId()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forum/post/archive/{id}", requirements={"id" = "\d+"}, name="forum_archive_post")
     * @param Conversation $post
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function archivePost(Conversation $post, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $post->setIsArchived(true);
        $em->flush();

        $this->addFlash(
            'success',
            'Conversation archivée avec succès!'
        );

        foreach($post->getAuthorizedUser() as $user) {
            $notificationService->add('Conversation', 'La conversation "'.$post->getTitle().'" a été archivée.', $user);
        }

        return $this->redirectToRoute('forum_index');
    }

    /**
     * @Route("/forum/post/desarchive/{id}", requirements={"id" = "\d+"}, name="forum_desarchive_post")
     * @param Conversation $post
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function desarchivePost(Conversation $post, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $post->setIsArchived(false);
        $em->flush();

        $this->addFlash(
            'success',
            'Conversation désarchivée avec succès!'
        );

        foreach($post->getAuthorizedUser() as $user) {
            $notificationService->add('Conversation', 'La conversation "'.$post->getTitle().'" a été désarchivée.', $user);
        }

        return $this->redirectToRoute('forum_show_archived');
    }

    /**
     * @Route("/forum/post/delete/{id}", name="forum_delete_post")
     * @param Conversation $post
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePost(Conversation $post, NotificationService $notificationService) {

        foreach($post->getAuthorizedUser() as $user) {
            $notificationService->add('Conversation', 'La conversation "'.$post->getTitle().'" a été supprimée.', $user);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash(
            'success',
            'Conversation supprimé avec succès!'
        );

        return $this->redirectToRoute('forum_index');
    }
}