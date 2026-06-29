<?php

namespace App\Controller\Api;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        ClientRepository $clientRepository,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password required'], 400);
        }

        $client = $clientRepository->findOneBy(['email' => $email]);

        if (!$client || !$hasher->isPasswordValid($client, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $token = $jwtManager->create($client);

        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => $client->getId(),
                'name' => $client->getName(),
            ],
        ]);
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwtManager,
        ClientRepository $clientRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$name || !$email || !$password) {
            return new JsonResponse(['error' => 'Name, email and password required'], 400);
        }

        if ($clientRepository->findOneBy(['email' => $email])) {
            return new JsonResponse(['error' => 'Email already in use'], 409);
        }

        $client = new Client();
        $client->setName($name);
        $client->setEmail($email);
        $client->setPassword($hasher->hashPassword($client, $password));
        $client->setRoles(['ROLE_CLIENT']);
        $client->setIsBanned(false);

        $em->persist($client);
        $em->flush();

        $token = $jwtManager->create($client);

        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => $client->getId(),
                'name' => $client->getName(),
            ],
        ], 201);
    }
}
