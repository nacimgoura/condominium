<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Form\MeetingType;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MeetingController extends Controller
{
    /**
     * @Route("/meeting", name="meeting_index")
     */
    public function index() {

        $listMeeting = $this->getDoctrine()
            ->getRepository(Meeting::class)
            ->findOwnMeeting($this->getUser());

        return $this->render('meeting/index.html.twig', [
            'listMeeting' => $listMeeting
        ]);
    }

    /**
     * @Route("/meeting/add", name="meeting_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addMeeting(Request $request, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted('ROLE_MANAGER', null, 'Unable to access this page!');

        $meeting = new Meeting();

        $meeting->setCondominium($this->getUser()->getCondominium());

        $form = $this->createForm(MeetingType::class, $meeting, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $meeting = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($meeting);
            $em->flush();

            $this->addFlash(
                'success',
                'Réunion ajoutée avec succès!'
            );

            foreach($meeting->getCondominium()->getUser() as $user) {
                $notificationService->add('Réunion', 'Une nouvelle réunion nommé "'.$meeting->getTitle().'" à été créé.', $user);
            }

            return $this->redirectToRoute('meeting_index');
        }

        return $this->render('meeting/form.html.twig', [
            'action' => $this->generateUrl('meeting_add'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/meeting/edit/{id}", name="meeting_edit")
     * @param Request $request
     * @param Meeting $meeting
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editMeeting(Request $request, Meeting $meeting, NotificationService $notificationService) {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        $form = $this->createForm(MeetingType::class, $meeting, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $meeting = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->merge($meeting);
            $em->flush();

            $this->addFlash(
                'success',
                'Réunion modifié avec succès!'
            );

            foreach($meeting->getCondominium()->getUser() as $user) {
                $notificationService->add('Réunion', 'La réunion "'.$meeting->getTitle().'" à été modifié.', $user);
            }

            return $this->redirectToRoute('meeting_index');
        }

        return $this->render('meeting/form.html.twig', [
            'action' => $this->generateUrl('meeting_edit', ['id' => $meeting->getId()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/meeting/delete/{id}", name="meeting_delete")
     * @param Meeting $meeting
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteMeeting(Meeting $meeting, NotificationService $notificationService) {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        foreach($meeting->getCondominium()->getUser() as $user) {
            $notificationService->add('Réunion', 'La réunion "'.$meeting->getTitle().'" à été supprimé.', $user);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($meeting);
        $em->flush();

        $this->addFlash(
            'success',
            'Réunion supprimée avec succès!'
        );

        return $this->redirectToRoute('meeting_index');
    }

}