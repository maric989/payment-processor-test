<?php

namespace App\Application\Request;

use App\Application\Contract\BaseRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseRequest implements BaseRequestInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly RequestStack $requestStack
    ) {
        $this->populate();
        $this->validate();
    }

    public function populate(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            $data = [];
        }

        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    public function validate(): void
    {
        $violations = $this->validator->validate($this);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            $response = new JsonResponse(['errors' => $errors], 400);
            $response->send();
            exit;
        }
    }
}