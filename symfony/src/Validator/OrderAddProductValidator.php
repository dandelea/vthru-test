<?php

namespace App\Validator;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class OrderAddProductValidator
{
    public function validate($parameters)
    {
        /* @var $constraint \App\Validator\Product */

        $constraint = new Assert\Collection([
            'id' => [
                new Assert\Required,
                new Assert\GreaterThan(0),
            ],
            'products' => [
                new Assert\Required,
            ],
        ]);

        $validator = Validation::createValidator();
        $violations = $validator->validate([
            'id' => $parameters['id'],
            'products' => $parameters['products'],
        ], $constraint);

        if (0 !== $violations->count()) {
            throw new InvalidParameterException('Invalid arguments');
        }

        $constraint = new Assert\Collection([
            'id' => [
                new Assert\Required,
                new Assert\GreaterThan(0),
            ],
            'quantity' => [
                new Assert\Required,
                new Assert\GreaterThan(0),
            ],
        ]);


        foreach ($parameters['products'] as $i => $product) {
            $violations = $validator->validate([
                'id' => $product['id'],
                'quantity' => $product['quantity'],
            ], $constraint);
            if (0 !== $violations->count()) {
                throw new InvalidParameterException('Invalid arguments');
            }
        }
    }
}
