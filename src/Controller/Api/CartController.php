<?php

namespace App\Controller\Api;

use App\Entity\Client;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/cart')]
class CartController extends AbstractController
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'api_cart_show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $cart = $this->cartRepository->findOrCreateForClient($this->getClient());

        return $this->json($this->hydrate($cart));
    }

    #[Route('/items', name: 'api_cart_add_item', methods: ['POST'])]
    public function addItem(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $productId = (int) ($data['productId'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? 1);

        $product = $this->productRepository->find($productId);
        if (!$product) {
            return $this->json(['message' => 'Product not found.'], 404);
        }

        $cart = $this->cartRepository->findOrCreateForClient($this->getClient());
        $newQuantity = $cart->getQuantityFor($productId) + $quantity;
        $cart->setQuantityFor($productId, $newQuantity);

        $this->em->flush();

        return $this->json($this->hydrate($cart));
    }

    #[Route('/items/{productId}', name: 'api_cart_update_item', methods: ['PATCH'])]
    public function updateItem(int $productId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $quantity = (int) ($data['quantity'] ?? 0);

        $cart = $this->cartRepository->findOrCreateForClient($this->getClient());
        $cart->setQuantityFor($productId, $quantity);

        $this->em->flush();

        return $this->json($this->hydrate($cart));
    }

    #[Route('/items/{productId}', name: 'api_cart_remove_item', methods: ['DELETE'])]
    public function removeItem(int $productId): JsonResponse
    {
        $cart = $this->cartRepository->findOrCreateForClient($this->getClient());
        $cart->removeProduct($productId);

        $this->em->flush();

        return $this->json($this->hydrate($cart));
    }

    #[Route('/merge', name: 'api_cart_merge', methods: ['POST'])]
    public function merge(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $items = $data['items'] ?? [];

        $cart = $this->cartRepository->findOrCreateForClient($this->getClient());

        foreach ($items as $item) {
            $productId = (int) ($item['productId'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $cart->setQuantityFor($productId, $cart->getQuantityFor($productId) + $quantity);
        }

        $this->em->flush();

        return $this->json($this->hydrate($cart));
    }

    private function getClient(): Client
    {
        /** @var Client $client */
        $client = $this->getUser();

        return $client;
    }

    private function hydrate(\App\Entity\Cart $cart): array
    {
        $lines = $cart->getProducts();
        $productIds = array_column($lines, 'productId');
        $products = $this->productRepository->findBy(['id' => $productIds]);
        $productsById = [];
        foreach ($products as $product) {
            $productsById[$product->getId()] = $product;
        }

        $items = [];
        $total = 0.0;

        foreach ($lines as $line) {
            $product = $productsById[$line['productId']] ?? null;
            if (!$product) {
                continue; // product was deleted since being added
            }
            $lineTotal = (float) $product->getPrice() * $line['quantity'];
            $total += $lineTotal;

            $items[] = [
                'productId' => $product->getId(),
                'slug' => $product->getSlug(),
                'name' => $product->getName(),
                'imageUrl' => $product->getImageUrl(),
                'price' => $product->getPrice(),
                'quantity' => $line['quantity'],
                'lineTotal' => $lineTotal,
            ];
        }

        return [
            'items' => $items,
            'totalItems' => array_sum(array_column($items, 'quantity')),
            'totalPrice' => $total,
        ];
    }
}