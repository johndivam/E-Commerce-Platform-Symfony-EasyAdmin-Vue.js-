<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $products = $this->productRepository->getWithPagination($request->query->getInt('page', 1), 12);

        $json = $this->serializer->serialize($products, 'json', [
            'groups' => ['product:read'],
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Product not found.'], 404);
        }

        $json = $this->serializer->serialize($product, 'json', [
            'groups' => ['product:read'],
        ]);

        return new JsonResponse($json, 200, [], true);
    }
}