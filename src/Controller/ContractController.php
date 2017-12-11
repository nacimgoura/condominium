<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Contract;

class ContractController extends Controller
{
    /**
     * @Route("/contract", name="contract_index")
     */
    public function index() {
        return $this->render('contract/index.html.twig');
    }
}
