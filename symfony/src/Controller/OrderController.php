<?php

namespace App\Controller;

use App\Entity\OrderProduct;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Validator\OrderAddProductValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class OrderController
{

    private $orderRepository;
    private $productRepository;

    private $orderAddProductValidator;

    public function __construct(OrderRepository $orderRepository, ProductRepository $productRepository, OrderAddProductValidator $orderAddProductValidator)
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->orderAddProductValidator = $orderAddProductValidator;
    }

    /**
     * @Route("/order", methods="POST")
     */
    public function create(): JsonResponse
    {
        $this->orderRepository->createOrder();

        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/order/{id}", methods="GET")
     */
    public function getOrder($id): JsonResponse
    {
        $order = $this->orderRepository->findOneBy(['id' => $id]);
        if (empty($order)) throw new NotFoundHttpException('Order not found');

        $products = $order->getOrderProducts()->map(function ($orderProduct) {
            return [
                'id' => $orderProduct->getProduct()->getId(),
                'name' => $orderProduct->getProduct()->getName(),
                'quantity' => $orderProduct->getQuantity(),
                'price' => $orderProduct->getProduct()->getPrice(),
            ];
        });

        return new JsonResponse([
            'id' => $order->getId(),
            'date' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'total_price' => $order->getTotalPrice(),
            'products' => $products->toArray(),
        ]);
    }

    /**
     * @Route("/order/product/add", methods="POST")
     */
    public function addProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->orderAddProductValidator->validate($data);
        } catch (InvalidParameterException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $order = $this->orderRepository->findOneBy(['id' => $data['id']]);
        if (empty($order)) throw new NotFoundHttpException('Order not found');

        $orderProducts = array_map(function ($line) use ($order) {
            $product = $this->productRepository->findOneBy(['id' => $line['id']]);
            $result = new OrderProduct();
            $result->setQuantity($line['quantity']);
            $result->setProduct($product);
            $result->setOrder($order);
            return $result;
        }, $data['products']);

        $this->orderRepository->addProducts($order, $orderProducts);

        return new JsonResponse(['success' => true]);
    }
}
