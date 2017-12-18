<?php

namespace App\Controller;

use App\Entity\Condominium;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home_index")
     */
    public function index() {

        $condominium = null;
        if (in_array('ROLE_USER', $this->getUser()->getRoles())) {
            $condominium =  $this->getDoctrine()
                ->getRepository(Condominium::class)
                ->findOneById($this->getUser()->getCondominium()->getId());
        }

        return $this->render('home/index.html.twig', [
            'title' => 'Accueil',
            'user' => $this->getUser(),
            'condominium' => $condominium
        ]);
    }
}