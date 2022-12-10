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
    /**
     * Retourne toutes les colonnes et les cartes de tâches associées
     * @Route("/api/tasks", name="api_get_tasks", methods={"GET"})
     */
    public function apiGet(ColumnRepository $columnRepository): Response
    {   
        return $this->json($columnRepository->findAll(), 200, [], ['groups' => 'task_read']);
    }

    /**
     * Retourne la dernière tâche mise à jour (pour la mise à jour du client)
     * @Route("/api/tasks/last", name="api_post_tasks", methods={"GET"})
     */
    public function apiGetLastTask(TaskRepository $taskRepository): Response
    {
        return $this->json($taskRepository->getLastUpdatedTask(), 200, [], ['groups' => 'task_read']);
    }

    /**
     * Permet de poster une nouvelle carte de tâche
     * @Route("/api/column/{id}/task", name="api_post_task", methods={"POST"})
     * 
     */
    public function apiPostTask(
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Column $column
    ): Response
    {   
        $data = $request->getContent();
        // je récupère dans l'URL l'id de la colonne pour lui associer la nouvelle tâche
        $column = $doctrine->getRepository(Column::class)->find($column->getId());
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
            ['groups' => ['task_read']]
        );
    }

    /**
     * Permet de mettre à jour une carte de tâche sans modifier tout l'objet
     * @Route("/api/{column}/task/{id}", name="api_put_task", methods={"PATCH"})
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
            ['groups' => ['task_read']]
        );
    }

    /**
     * Permet de supprimer une carte de tâche
     * @Route("/api/task/{id}", name="api_delete_task", methods={"DELETE"})
     */
    public function apiDeleteTask(Task $task, EntityManagerInterface $doctrine): Response
    {   
        $doctrine->remove($task);
        $doctrine->flush();

        return $this->json(
            $task,
            Response::HTTP_OK,
            [],
            ['groups' => ['task_read']]
        );
    }

    /**
     * Permet de poster une nouvelle colonne sans audune carte de tâche associée
     * @Route("/api/column", name="api_post_column", methods={"POST"})
     */
    public function apiPostColumn(
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
            ['groups' => ['task_read']]
        );
    }

    /**
     * Permet de modifier une colonne et ses cartes de tâches associées
     * @Route("/api/column/{id}", name="api_put_tasks", methods={"PATCH"})
     */
    public function apiPutColumn(
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
            ['groups' => ['task_read']]
        );
    }

    /**
     * Permet de supprimer une colonne et ses cartes de tâches associées
     * @Route("/api/column/{id}", name="api_delete_tasks", methods={"DELETE"})
     */

    public function apiDeleteColumn(Column $column, EntityManagerInterface $doctrine): Response
    {   
        $doctrine->remove($column);
        $doctrine->flush();

        return $this->json(
            $column,
            Response::HTTP_OK,
            [],
            ['groups' => ['task_read']]
        );
    }



}
