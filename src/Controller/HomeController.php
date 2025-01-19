<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {
            return $this->json([
                'message' => 'On est déjà connecté et il n\'y a rien à voir ici Mon TaskManager est une API',
            ]);
        }
    }
}
