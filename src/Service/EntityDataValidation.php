<?php
namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EntityDataValidation
{
    private $validatorInterface;

    public function __construct(ValidatorInterface $validatorInterface)
    {
        $this->validatorInterface = $validatorInterface;
    }

    public function validateData($entity)
    {   
        
        $errors = $this->validatorInterface->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // If no errors return null
        return null;
    }
}
