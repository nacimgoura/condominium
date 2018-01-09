<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Sondage;
use App\Form\SondageType;
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
     * @param $id
     * @param bool $isInProject
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetailSondage($id, $isInProject=false) {

        $sondage = $this->getDoctrine()
            ->getRepository(Sondage::class)
            ->findOneBy([
                'id' => $id
            ]);

        $ownAnswer = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->findOneBy([
                'sondage' => $sondage->getId()
            ]);

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addSondage(Request $request) {

        $sondage = new Sondage();

        $form = $this->createForm(SondageType::class, $sondage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sondage = $form->getData();
            $sondage->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->merge($sondage);
            $em->flush();

            $this->addFlash(
                'success',
                'Sondage ajouté avec succès!'
            );

            return $this->redirectToRoute('sondage_index');
        }

        return $this->render('sondage/form.html.twig', [
            'action' => $this->generateUrl('sondage_add'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sondage/edit/{id}", requirements={"id" = "\d+"}, name="sondage_edit")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editSondage($id, Request $request) {

        $sondage = $this->getDoctrine()
            ->getRepository(Sondage::class)
            ->findOneBy([
                'id' => $id,
                'user' => $this->getUser()
            ]);

        if (!$sondage) {
            throw $this->createNotFoundException('Ce sondage n\'existe pas');
        }

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
            'action' => $this->generateUrl('sondage_edit', ['id' => $id]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sondage/delete/{id}", requirements={"id" = "\d+"}, name="sondage_delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteSondage($id) {

        $sondage = $this->getDoctrine()
            ->getRepository(Sondage::class)
            ->findOneBy([
                'id' => $id,
                'user' => $this->getUser()
            ]);

        if (!$sondage) {
            throw $this->createNotFoundException('Ce sondage n\'existe pas');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($sondage);
        $em->flush();

        $this->addFlash(
            'success',
            'Sondage supprimée avec succès!'
        );

        return $this->redirectToRoute('sondage_index');
    }
}