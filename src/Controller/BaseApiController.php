<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class BaseApiController extends AbstractController
{
    protected function validationErrorResponse(
        ConstraintViolationListInterface $errors
    ) : JsonResponse {
        $result = [];

        foreach($errors as $error) {
            $result[$error->getPropertyPath()] = $error->getMessage();
        }

        return $this->json(
            ['errors' => $result],
            400);
    }
}