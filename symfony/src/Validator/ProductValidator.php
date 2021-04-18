<?php

namespace App\Validator;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class ProductValidator
{
    public function validate($parameters)
    {
        /* @var $constraint \App\Validator\Product */

        $constraint = new Assert\Collection([
            'name' => [
                new Assert\Required,
                new Assert\NotBlank
            ],
            'price' => [
                new Assert\Required,
                new Assert\GreaterThan(0),
            ],
        ]);

        $validator = Validation::createValidator();
        $violations = $validator->validate([
            'name' => $parameters['name'],
            'price' => $parameters['price'],
        ], $constraint);

        if (0 !== $violations->count()) {
            throw new InvalidParameterException('Invalid arguments');
        }
    }
}
