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
        return $this->render('charge/index.html.twig');
    }

}