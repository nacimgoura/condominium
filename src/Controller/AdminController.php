<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function index()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }

    /**
     * @Route("/admin/user", name="admin_gestion_user")
     */
    public function gestionUser()
    {
        return new Response('<html><body>User gestion page!</body></html>');
    }
}