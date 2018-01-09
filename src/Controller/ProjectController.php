<?php

namespace App\Controller;

use App\Entity\Project;
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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail($id) {

        $project = $this->getDoctrine()
            ->getRepository(Project::class)
            ->findOneBy([
                'id' => $id
            ]);

        if (!$project) {
            throw $this->createNotFoundException('Ce projet n\'existe pas');
        }

        return $this->render('project/detail.html.twig', [
            'project' => $project
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
        $project->setAuthorizedUser($this->getUser()->getCondominium()->getUser());

        $form = $this->createForm(ProjectType::class, $project, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $this->addFlash(
                'success',
                'Projet ajouté avec succès!'
            );

            foreach($project->getAuthorizedUser() as $user) {
                $notificationService->add('Projet', 'Vous avez été ajouté au projet '.$project->getTitle(), $user);
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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editProject($id) {

    }

    /**
     * @Route("/project/delete/{id}", requirements={"id" = "\d+"}, name="project_delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProject($id) {

        $project = $this->getDoctrine()
            ->getRepository(Project::class)
            ->findOneBy([
                'id' => $id,
            ]);

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
