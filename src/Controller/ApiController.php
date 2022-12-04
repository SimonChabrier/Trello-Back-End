<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Entity\Column;
use App\Repository\ColumnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/tasks", name="api_get_tasks", methods={"GET"})
     */
    public function apiGet(ColumnRepository $columnRepository): Response
    {   
        return $this->json($columnRepository->findAll(), 200, [], ['groups' => 'task_read']);
    }

    /**
     * @Route("/api/tasks", name="api_post_tasks", methods={"POST"})
     */
    public function apiPost(
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response
    {   
    
        $data = $request->getContent();
        $task = $serializer->deserialize($data, Column::class, 'json');
        $errors = $validator->validate($task);

        if (count($errors) > 0) {
            // les messages d'erreurs sont à définir dans les asserts de l'entité Column
            // Ex: @Assert\NotBlank(message = "Mon message")
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $doctrine->persist($task);
        $doctrine->flush();

        // return $this->json(
        //     $task, 
        //     201, 
        //     [], 
        //     ['groups' => 'task_read']);

        return $this->json(
            $task,
            Response::HTTP_CREATED,
            [],
            ['groups' => ['task_read']]
        );
    }

}
