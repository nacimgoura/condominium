<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MeetingController extends Controller
{
    /**
     * @Route("/meeting", name="meeting_index")
     */
    public function index() {
        return $this->render('meeting/index.html.twig');
    }
}