<?php

namespace App\Controller;

use App\Entity\Sondage;
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

        $ob = new Highchart();
        $ob->chart->renderTo('responsechart');  // The #id of the div where to render the chart
        $ob->title->text('Répartition des réponses');
        $ob->plotOptions->pie(array(
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'dataLabels'    => array('enabled' => false),
            'showInLegend'  => true
        ));
        $data = array(
            array('Firefox', 45.0),
            array('IE', 26.8),
            array('Chrome', 12.8),
            array('Safari', 8.5),
            array('Opera', 6.2),
            array('Others', 0.7),
        );
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
     */
    public function addSondage() {

    }
}