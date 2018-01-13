<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Sondage;
use App\Form\SondageType;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SondageController extends Controller
{
    /**
     * @Route("/sondage", name="sondage_index")
     */
    public function index() {

        $listSondage = $this->getDoctrine()
            ->getRepository(Sondage::class)
            ->findOwnSondage($this->getUser());

        return $this->render('sondage/index.html.twig', [
            'listSondage' => $listSondage
        ]);
    }

    /**
     * @Route("/sondage/{id}/{isInProject}", requirements={"id" = "\d+"}, name="sondage_detail")
     * @param Sondage $sondage
     * @param bool $isInProject
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetailSondage(Sondage $sondage, $isInProject=false) {

        $ownAnswer = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->findOneBySondage($sondage->getId());

        $listAnswer = [];
        foreach($sondage->getAnswer() as $answer) {
            array_push($listAnswer, $answer->getTitle());
        }

        $data = [];
        foreach (array_count_values($listAnswer) as $key => $value) {
            array_push($data, [$key, $value]);
        }

        $ob = new Highchart();
        $ob->chart->renderTo('responsechart');
        $ob->title->text('Répartition des réponses');
        $ob->plotOptions->pie([
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'dataLabels'    => [
                'enabled' => true,
                'format' => '<b>{point.name}</b>: {point.percentage:.1f} %'
            ],
            'showInLegend'  => true
        ]);
        $ob->series([[
            'type' => 'pie',
            'name' => 'response',
            'data' => $data
        ]]);

        return $this->render('sondage/detail.html.twig', [
            'sondage' => $sondage,
            'ownAnswer' => $ownAnswer,
            'isInProject' => $isInProject,
            'chart' => $ob
        ]);
    }

    /**
     * @Route("/sondage/add", name="sondage_add")
     * @param Request $request
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addSondage(Request $request, NotificationService $notificationService) {

        $sondage = new Sondage();
        $sondage->setUser($this->getUser());
        $sondage->setCondominium($this->getUser()->getCondominium());

        $form = $this->createForm(SondageType::class, $sondage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sondage = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($sondage);
            $em->flush();

            $this->addFlash(
                'success',
                'Sondage ajouté avec succès!'
            );

            foreach($this->getUser()->getCondominium()->getUser() as $user) {
                $notificationService->add('Sondage', 'Un sondage à été créé "'.$sondage->getQuestion().'"', $user);
            }

            return $this->redirectToRoute('sondage_index');
        }

        return $this->render('sondage/form.html.twig', [
            'action' => $this->generateUrl('sondage_add'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sondage/edit/{id}", requirements={"id" = "\d+"}, name="sondage_edit")
     * @param Sondage $sondage
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editSondage(Sondage $sondage, Request $request) {

        $form = $this->createForm(SondageType::class, $sondage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sondage = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->merge($sondage);
            $em->flush();

            $this->addFlash(
                'success',
                'Sondage édité avec succès!'
            );

            return $this->redirectToRoute('sondage_index');
        }

        return $this->render('sondage/form.html.twig', [
            'action' => $this->generateUrl('sondage_edit', ['id' => $sondage->getId()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sondage/delete/{id}", requirements={"id" = "\d+"}, name="sondage_delete")
     * @param Sondage $sondage
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteSondage(Sondage $sondage, NotificationService $notificationService) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($sondage);
        $em->flush();

        foreach($this->getUser()->getCondominium()->getUser() as $user) {
            $notificationService->add('Sondage', 'Le sondage "'.$sondage->getQuestion().'" à été supprimé.', $user);
        }

        $this->addFlash(
            'success',
            'Sondage supprimée avec succès!'
        );

        return $this->redirectToRoute('sondage_index');
    }
}