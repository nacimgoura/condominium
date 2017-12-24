<?php

namespace App\Controller;

use App\Entity\Charge;
use App\Entity\Condominium;
use App\Entity\Conversation;
use App\Entity\Notification;
use App\Entity\Payment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home_index")
     */
    public function index() {

        $condominium = null;
        $listPost = null;
        $listCharge = null;
        $listPayment = null;
        if (in_array('ROLE_USER', $this->getUser()->getRoles())) {
            $condominium =  $this->getDoctrine()
                ->getRepository(Condominium::class)
                ->findOneById($this->getUser()->getCondominium()->getId());
            $listPost = $this->getDoctrine()
                ->getRepository(Conversation::class)
                ->findAllAuthorized($this->getUser());
            $listCharge = $this->getDoctrine()
                ->getRepository(Charge::class)
                ->findOwnCharge($this->getUser());
            $listPayment = $this->getDoctrine()
                ->getRepository(Payment::class)
                ->findByUser($this->getUser());
        }

        return $this->render('home/index.html.twig', [
            'title' => 'Accueil',
            'user' => $this->getUser(),
            'condominium' => $condominium,
            'listPost' => $listPost,
            'listCharge' => $listCharge,
            'listPayment' => $listPayment
        ]);
    }

    /**
     * affiche la navbar et ses actions
     */
    public function navbar() {
        $listNotif = $this->getDoctrine()
            ->getRepository(Notification::class)
            ->findBy(['user' => $this->getUser(), 'createdAt' => new \DateTime()]);

        return $this->render('navbar/navbar.html.twig',[
            "listNotif" => $listNotif,
        ]);
    }
}