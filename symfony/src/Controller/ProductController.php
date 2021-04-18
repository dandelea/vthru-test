<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Validator\ProductValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ProductController
{
    private $productRepository;

    private $productValidator;

    public function __construct(ProductRepository $productRepository, ProductValidator $productValidator)
    {
        $this->productRepository = $productRepository;
        $this->productValidator = $productValidator;
    }

    /**
     * @Route("/product", methods="POST")
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->productValidator->validate($data);
        } catch (InvalidParameterException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->productRepository->createProduct($data['name'], $data['price']);

        return new JsonResponse([
            'id' => $result->getId(),
            'name' => $result->getName(),
            'price' => $result->getPrice(),
        ]);
    }
}
