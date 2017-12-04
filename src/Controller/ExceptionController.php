<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\FlattenException;

class ExceptionController extends Controller
{
    public function showException(FlattenException $exception) {
        switch ($exception->getStatusCode()) {
            case '404':
                return $this->render('error/404.html.twig');
        }
    }
}