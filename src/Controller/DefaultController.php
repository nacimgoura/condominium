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
        $listPost = null;
        $listCharge = null;
        if (in_array('ROLE_USER', $this->getUser()->getRoles())) {
            $listPost = $this->getDoctrine()
                ->getRepository(Conversation::class)
                ->findAllAuthorized($this->getUser());
            $listCharge = $this->getDoctrine()
                ->getRepository(Charge::class)
                ->findOwnCharge($this->getUser());
        }

        return $this->render('home/index.html.twig', [
            'title' => 'Accueil',
            'user' => $this->getUser(),
            'listPost' => $listPost,
            'listCharge' => $listCharge
        ]);
    }

    /**
     * affiche la navbar et ses actions
     */
    public function navbar() {
        $listNotif = $this->getDoctrine()
            ->getRepository(Notification::class)
            ->findNotifToday($this->getUser());

        return $this->render('navbar/navbar.html.twig',[
            "listNotif" => $listNotif,
        ]);
    }
}