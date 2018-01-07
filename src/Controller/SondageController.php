<?php

namespace App\Controller;

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
            ->findByUser($this->getUser()->getId());

        return $this->render('sondage/index.html.twig', [
            'listSondage' => $listSondage
        ]);
    }

    /**
     * @Route("/sondage/{id}", requirements={"id" = "\d+"}, name="sondage_detail")
     */
    public function showDetailSondage($id) {

        $sondage = $this->getDoctrine()
            ->getRepository(Sondage::class)
            ->find($id);

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
            'dataLabels'    => ['enabled' => false],
            'showInLegend'  => true
        ]);
        $ob->series([[
            'type' => 'pie',
            'name' => 'response',
            'data' => $data
        ]]);

        return $this->render('sondage/detail.html.twig', [
            'sondage' => $sondage,
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
        $sondage->setUser($this->getUser()->getId());

        $form = $this->createForm(SondageType::class, $sondage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sondage = $form->getData();

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
            ->find($id);

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
}