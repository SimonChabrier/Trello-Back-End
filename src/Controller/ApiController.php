<?php

namespace App\Controller;

use App\Repository\ColumnRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/tasks", name="app_api")
     */
    public function index(ColumnRepository $columnRepository): Response
    {   

        return $this->json($columnRepository->findAll(), 200, [], ['groups' => 'task_read']);

    }
}
