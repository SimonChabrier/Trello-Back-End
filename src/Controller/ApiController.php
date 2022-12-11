<?php

namespace App\Controller;

use Exception;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\Column;
use App\Repository\ColumnRepository;
use App\Repository\TaskRepository;
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


    //////////////////////////////////////////////* GET 
    //////* JSON RETOURNE : 
            // {
            // 	"id": 187,
            // 	"column_name": "Nom de la colonne",
            // 	"tasks": [
            // 		{
            // 			"id": 205,
            // 			"task_title": "",
            // 			"task_content": "",
            // 			"task_done": false,
            // 			"column_number": "2",
            // 			"card_number": "1",
            // 			"card_color": "card--color--red",
            // 			"textarea_height": "150",
            // 			"users": []
            // 		}
            // 	]
            // }

    /**
     * Retourne l'ensemble des colonnes et l'ensemble des cartes de tâches associées
     * @Route("/api/tasks", name="api_get_data", methods={"GET"})
     */
    public function apiGet(ColumnRepository $columnRepository): Response
    {   
        return $this->json(
            $columnRepository->findAll(),
            Response::HTTP_OK, 
            [], 
            ['groups' => 'tasks_read']
        );
    }

    /**
     * Retourne la dernière tâche créee
     * @Route("/api/tasks/last", name="api_last_task", methods={"GET"})
     */
    public function apiGetLastCreatedTask(TaskRepository $taskRepository): Response
    {
        return $this->json(
            $taskRepository->getLastCreatedTask(), 
            Response::HTTP_OK,  
            [], 
            ['groups' => 'tasks_read']
        );
    }

    /**
     * Retourne la dernière colonne créee
     * @Route("/api/columns/last", name="api_last_column", methods={"GET"})
     */
    public function apiGetLastCreatedColumn(ColumnRepository $columnRepository): Response
    {
        return $this->json(
            $columnRepository->getLastCreatedColumn(), 
            Response::HTTP_OK, 
            [], 
            ['groups' => 'tasks_read']
        );
    }

    //////////////////////////////////////////////* POST

    /**
     * Permet de poster une nouvelle colonne sans aucune carte de tâche associée
     * @Route("/api/column", name="api_post_column", methods={"POST"})
     */
    public function apiPostNewColumn(
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

        return $this->json(
            $task,
            Response::HTTP_CREATED,
            [],
            ['groups' => ['tasks_read']]
        );
    }

    /**
     * Permet de poster une nouvelle carte de tâche dans une colonne
     * @Route("/api/tasks/{column}", name="api_post_task", methods={"POST"})
     * 
     */
    public function apiPostNewTask(
        EntityManagerInterface $doctrine,
        Request $request,
        Column $column,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response
    {   
        $data = $request->getContent();
        $task = $serializer->deserialize($data, Task::class, 'json');
        $task->setTaskColumn($column);


        $errors = $validator->validate($task);
        if (count($errors) > 0) {
            // les messages d'erreurs sont à définir dans les asserts de l'entité Column
            // Ex: @Assert\NotBlank(message = "Mon message")
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $doctrine->persist($task);
        $doctrine->flush();

        return $this->json(
            $task,
            Response::HTTP_CREATED,
            [],
            ['groups' => ['task_write']]
        );
    }

    //////////////////////////////////////////////* PATCH

    /**
     * Permet de mettre à jour une colonne sans modifier tout l'objet
     * @Route("/api/column/{column}", name="api_patch_column", methods={"PATCH"})
     */
    public function apiPatchColumn(
        Column $column,
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response
    {   
        $data = $request->getContent();
        $serializer->deserialize($data, Column::class, 'json', ['object_to_populate' => $column]);
        $errors = $validator->validate($column);

        if (count($errors) > 0) {
            // les messages d'erreurs sont à définir dans les asserts de l'entité Column
            // Ex: @Assert\NotBlank(message = "Mon message")
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $doctrine->persist($column);
        $doctrine->flush();

        return $this->json(
            $column,
            Response::HTTP_OK,
            [],
            ['groups' => ['tasks_read']]
        );
    }

    /**
     * Permet de mettre à jour une carte de tâche sans modifier tout l'objet
     * @Route("/api/{column}/task/{task}", name="api_patch_task", methods={"PATCH"})
     */
    public function apiPatchTask(
        Column $column,
        Task $task,
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response
    {   
        $data = $request->getContent();
        
        $serializer->deserialize($data, Task::class, 'json', ['object_to_populate' => $task]);
    
        $errors = $validator->validate($task);

        // Mise à jour de l'Id de la colonne pour la carte de tâche
        $task = $doctrine->getRepository(Task::class)->find($task->getId());
        $task->setTaskColumn($column);

        if (count($errors) > 0) {
            // les messages d'erreurs sont à définir dans les asserts de l'entité Column
            // Ex: @Assert\NotBlank(message = "Mon message")
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $doctrine->persist($task);
        $doctrine->flush();

        return $this->json(
            $task,
            Response::HTTP_OK,
            [],
            ['groups' => ['tasks_read']]
        );
    }

    //////////////////////////////////////////////* DELETE

    /**
     * Permet de supprimer une carte de tâche
     * @Route("/api/task/{task}", name="api_delete_task", methods={"DELETE"})
     */
    public function apiDeleteTask(Task $task, EntityManagerInterface $doctrine): Response
    {   
        $doctrine->remove($task);
        $doctrine->flush();

        return $this->json(
            $task,
            Response::HTTP_OK,
            [],
            ['groups' => ['tasks_read']]
        );
    }

    /**
     * Permet de supprimer une colonne et ses cartes de tâches associées (cascade={"remove"} sur tasks dans l'entité Column)
     * @Route("/api/column/{column}", name="api_delete_tasks", methods={"DELETE"})
     */

    public function apiDeleteColumn(Column $column, EntityManagerInterface $doctrine): Response
    {   
        $doctrine->remove($column);
        $doctrine->flush();

        return $this->json(
            $column,
            Response::HTTP_OK,
            [],
            ['groups' => ['tasks_read']]
        );
    }



}
