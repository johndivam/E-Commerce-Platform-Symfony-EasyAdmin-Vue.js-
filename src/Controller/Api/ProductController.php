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
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 12;

        $paginator = $this->productRepository->getWithPagination($page, $limit,  $request->query->get('q'));
        $total = count($paginator);

        $json = $this->serializer->serialize([
            'items' => iterator_to_array($paginator->getIterator()),
            'total' => $total,
            'page' => $page,
            'totalPages' =>  (int) ceil($total / $limit),
        ], 'json', [
            'groups' => ['product:read'],
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{slug}', methods: ['GET'])]
    public function show(string $slug): JsonResponse
    {
        $product = $this->productRepository->findOneBySlugWithRelations($slug);

        if (!$product) {
            return new JsonResponse(['message' => 'Product not found.'], 404);
        }

        $json = $this->serializer->serialize($product, 'json', [
            'groups' => ['product:read'],
        ]);

        return new JsonResponse($json, 200, [], true);
    }
}