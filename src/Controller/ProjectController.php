<?php

namespace App\Controller;

use App\Entity\Journalisation;
use App\Entity\Project;
use App\Form\JournalisationProjectType;
use App\Form\ProjectType;
use App\Service\EmailService;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    /**
     * @Route("/project", name="project_index")
     */
    public function index() {

        $listProject = $this->getDoctrine()
            ->getRepository(Project::class)
            ->findAllAuthorized($this->getUser());

        return $this->render('project/index.html.twig', [
            'listProject' => $listProject
        ]);
    }

    /**
     * @Route("/project/{id}", requirements={"id" = "\d+"}, name="project_detail")
     * @param Request $request
     * @param Project $project
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail(Request $request, Project $project, NotificationService $notificationService) {

        $journalisation = new Journalisation();

        $form = $this->createForm(JournalisationProjectType::class, $journalisation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $journalisation = $form->getData();

            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move('attachment', $fileName);
                $journalisation->setAttachment($fileName);
                $project->setListAttachment(array_merge($project->getListAttachment(), [$fileName]));
                $em->merge($project);
            }

            $journalisation->setUser($this->getUser());
            $journalisation->setProject($project);

            $em->persist($journalisation);
            $em->flush();

            foreach($project->getAuthorizedUser() as $user) {
                $notificationService->add('Projet', 'Une journalisation a été ajouté au projet "'.$project->getTitle().'"', $user);
            }

            $this->addFlash(
                'success',
                'Journalisation ajoutée avec succès!'
            );

        }

        return $this->render('project/detail.html.twig', [
            'project' => $project,
            'action' => $this->generateUrl('project_detail', ['id' => $project->getId()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/add", name="project_add")
     * @param Request $request
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addProject(Request $request, NotificationService $notificationService) {

        if ($this->getUser() && !$this->getUser()->getCondominium()) {
            throw $this->createNotFoundException('Cette page n\'existe pas!');
        }

        $project = new Project();
        $project->setUser($this->getUser());
        $project->setCondominium($this->getUser()->getCondominium());
        $project->setAuthorizedUser($this->getUser()->getCondominium()->getUser());

        $form = $this->createForm(ProjectType::class, $project, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project = $form->getData();

            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move('attachment', $fileName);
                $project->setAttachment($fileName);
                $project->setListAttachment(array_merge($project->getListAttachment(), [$fileName]));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $this->addFlash(
                'success',
                'Projet ajouté avec succès!'
            );

            foreach($project->getAuthorizedUser() as $user) {
                $notificationService->add('Projet', 'Vous avez été ajouté au projet "'.$project->getTitle().'"', $user);
            }

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/form.html.twig', [
            'project' => $project,
            'action' => $this->generateUrl('project_add'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/edit/{id}", requirements={"id" = "\d+"}, name="project_edit")
     * @param Request $request
     * @param Project $project
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editProject(Request $request, Project $project, NotificationService $notificationService) {

        $attachment = $project->getAttachment();
        $form = $this->createForm(ProjectType::class, $project, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project = $form->getData();

            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move('attachment', $fileName);
                $project->setAttachment($fileName);
                $project->setListAttachment(array_merge($project->getListAttachment(), [$fileName]));
            } else if ($attachment) {
                $project->setAttachment($attachment);
            }

            $em = $this->getDoctrine()->getManager();
            $em->merge($project);
            $em->flush();

            $this->addFlash(
                'success',
                'Projet modifié avec succès!'
            );

            foreach($project->getAuthorizedUser() as $user) {
                $notificationService->add('Projet', 'Le projet "'.$project->getTitle().'" à été modifié', $user);
            }

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/form.html.twig', [
            'project' => $project,
            'action' => $this->generateUrl('project_edit', ['id' => $project->getId()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/delete/{id}", requirements={"id" = "\d+"}, name="project_delete")
     * @param Project $project
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProject($id, NotificationService $notificationService) {

        // si on est manager ou admin on peut supprimer les projets de autres
        if (in_array('ROLE_MANAGER', $this->getUser()->getRoles()) || in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $project = $this->getDoctrine()
                ->getRepository(Project::class)
                ->find($id);
        } else {
            $project = $this->getDoctrine()
                ->getRepository(Project::class)
                ->findOneBy([
                    'id' => $id,
                    'user' => $this->getUser()
                ]);
        }

        foreach($project->getAuthorizedUser() as $user) {
            $notificationService->add('Projet', 'Le projet "'.$project->getTitle().'" à été supprimé.', $user);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();

        $this->addFlash(
            'success',
            'Projet supprimée avec succès!'
        );

        return $this->redirectToRoute('project_index');
    }
}
