<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Charge;

class ChargeController extends Controller
{
    /**
     * @Route("/charge", name="charge_index")
     */
    public function index() {
        $listCharge = $this->getDoctrine()
            ->getRepository(Charge::class)
            ->findAll();

        return $this->render('charge/index.html.twig', [
            'listCharge' => $listCharge
        ]);
    }

    /**
     * @Route("/charge/detail/{id}", requirements={"id" = "\d+"}, name="charge_detail")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail($id) {
        $charge = $this->getDoctrine()
            ->getRepository(Charge::class)
            ->find($id);

        return $this->render('charge/detail.html.twig', [
            'charge' => $charge
        ]);
    }

    /**
     * @Route("/charge/add", requirements={"id" = "\d+"}, name="charge_add")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCharge() {
        return $this->render('charge/add.html.twig');
    }

    /**
     * @Route("/charge/delete/{id}", requirements={"id" = "\d+"}, name="charge_delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCharge($id) {
        $charge = $this->getDoctrine()
            ->getRepository(Charge::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($charge);
        $em->flush();

        $this->addFlash(
            'success',
            'Charge supprimée avec succès!'
        );

        return $this->redirectToRoute('charge_index');
    }

    /**
     * @Route("/charge/pay/{id}", requirements={"id" = "\d+"}, name="charge_pay")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payCharge($id) {
        return $this->render('charge/payement.html.twig');
    }

}