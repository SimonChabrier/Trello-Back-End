<?php

namespace App\Controller;

use Exception;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\Column;
use App\Repository\TaskRepository;

use App\Repository\ColumnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiController extends AbstractController
{   

    /**
     * Retourne l'ensemble des colonnes et l'ensemble des cartes de tâches associées
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
            ['groups' => ['column_write']]
        );
    }

    /**
     * Permet de mettre à jour une carte de tâche sans modifier tout l'objet
     * @IsGranted("ROLE_ADMIN")
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
            ['groups' => ['task_write']]
        );
    }

    //////////////////////////////////////////////* DELETE

    /**
     * Permet de supprimer une carte de tâche
     * @IsGranted("ROLE_ADMIN")
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
            ['groups' => ['task_delete']]
        );
    }

    /**
     * Permet de supprimer une colonne et ses cartes de tâches associées (cascade={"remove"} sur tasks dans l'entité Column)
     * @IsGranted("ROLE_ADMIN")
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
            ['groups' => ['column_delete']]
        );
    }

    /**
     * Permet d'enregistrer un utilisateur
     * @IsGranted("ROLE_ADMIN")
     * @Route("/api/register", name="api_user_register", methods={"POST"})
     */
    public function apiUserRegister(
        EntityManagerInterface $doctrine,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        ParameterBagInterface $params,
        EmailVerifier $emailVerifier
    ): Response
    {   
    
        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');
        $errors = $validator->validate($user);

        // hash du mot de passe depuis la requête
        $user->setPassword(
            password_hash($user->getPassword(), PASSWORD_BCRYPT)
        );

        if (count($errors) > 0) {
            // les messages d'erreurs sont à définir dans les asserts de l'entité Column
            // Ex: @Assert\NotBlank(message = "Mon message")
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $doctrine->persist($user);
        $doctrine->flush();

        $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($params->get('admin_email'), 'TrelloBackEnd'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

         //TODO : notifcation admin 


        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [],
            ['groups' => ['user_read']]
        );
    }

}



