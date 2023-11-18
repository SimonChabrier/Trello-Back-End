<?php

namespace App\Controller;

use Exception;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\Column;
use App\Service\ConfirmAccount;
use App\Service\EntityDataValidation;
use App\Repository\TaskRepository;
use App\Repository\ColumnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
//  
/**
 * Route prefix and security for all routes in this controller
 * @IsGranted("ROLE_ADMIN")
 * @Route("/api")
 */
class ApiController extends AbstractController
{   

    /**
     * Return all columns and all tasks associated to each column
     * @Route("/tasks", name="api_get_data", methods={"GET"})
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
     * Return the last created task
     * @Route("/tasks/last", name="api_last_task", methods={"GET"})
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
     * Return the last created column
     * @Route("/columns/last", name="api_last_column", methods={"GET"})
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
     * Create a new column and return it
     * @Route("/column", name="api_post_column", methods={"POST"})
     */
    public function apiPostNewColumn(
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        EntityDataValidation $entityDataValidation
    ): Response
    {   
        $data = $request->getContent();
        $column = $serializer->deserialize($data, Column::class, 'json');
        $entityDataValidation->validateData($column);
        $doctrine->persist($column);
        $doctrine->flush();

        return $this->json(
            $column,
            Response::HTTP_CREATED,
            [],
            ['groups' => ['tasks_read']]
        );
    }

    /**
     * Create a new task associated to a column and return it
     * @Route("/tasks/{column}", name="api_post_task", methods={"POST"})
     */
    public function apiPostNewTask(
        EntityManagerInterface $doctrine,
        Request $request,
        Column $column,
        SerializerInterface $serializer,
        EntityDataValidation $entityDataValidation
    ): Response
    {   
        $data = $request->getContent();
        $task = $serializer->deserialize($data, Task::class, 'json');
        $task->setTaskColumn($column);
        $entityDataValidation->validateData($task);
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
     * Update a column without modifying the whole object
     * @Route("/column/{column}", name="api_patch_column", methods={"PATCH"})
     */
    public function apiPatchColumn(
        Column $column,
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        EntityDataValidation $entityDataValidation
    ): Response
    {   
        $data = $request->getContent();
        $serializer->deserialize($data, Column::class, 'json', ['object_to_populate' => $column]);
        $entityDataValidation->validateData($column);
        $doctrine->flush();

        return $this->json(
            $column,
            Response::HTTP_OK,
            [],
            ['groups' => ['column_write']]
        );
    }

    /**
     * Update a task without modifying the whole object
     * @Route("/{column}/task/{task}", name="api_patch_task", methods={"PATCH"})
     */
    public function apiPatchTask(
        Column $column,
        Task $task,
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        EntityDataValidation $entityDataValidation
    ): Response
    {   
        $data = $request->getContent();    
        $serializer->deserialize($data, Task::class, 'json', ['object_to_populate' => $task]);
        $task->setTaskColumn($column);
        $entityDataValidation->validateData($task);
        $doctrine->persist($task);
        $doctrine->flush();

        return $this->json(
            $task,
            Response::HTTP_OK,
            [],
            ['groups' => ['task_write']]
        );
    }

    //////////////////////////////////////////////* DELETE

    /**
     * Delete a task card and return it
     * @Route("/task/{task}", name="api_delete_task", methods={"DELETE"})
     */
    public function apiDeleteTask(Task $task, EntityManagerInterface $doctrine): Response
    {   
        $doctrine->remove($task);
        return $this->json(
            $task,
            Response::HTTP_OK,
            [],
            ['groups' => ['task_delete']
        ]);
    }

    /**
     * Delete a column and its associated task cards (cascade={"remove"} on tasks in the Column entity)
     * @Route("/column/{column}", name="api_delete_tasks", methods={"DELETE"})
     */
    public function apiDeleteColumn(Column $column, EntityManagerInterface $doctrine): Response
    {   
        $doctrine->remove($column);

        return $this->json(
            $column,
            Response::HTTP_OK,
            [],
            ['groups' => ['column_delete']]
        );
    }

    /**
     * User registration with email confirmation
     * Only the admin can register a new user (ROLE_ADMIN)
     * @Route("/register", name="api_user_register", methods={"GET", "POST"})
     */
    public function apiUserRegister(
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        ConfirmAccount $confirmAccount
    ): Response
    {   
        $data = $request->getContent();

        $user = $serializer->deserialize($data, User::class, 'json');
        $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
        
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $doctrine->persist($user);
        $doctrine->flush();

        $confirmAccount->sendBeforeRegisterMessage($user);

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [],
            ['groups' => ['user_read']]
        );
    }

}



