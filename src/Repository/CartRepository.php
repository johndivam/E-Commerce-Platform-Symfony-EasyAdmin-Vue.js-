<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findOrCreateForClient(Client $client): Cart
    {
        $cart = $this->findOneBy(['client' => $client]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setClient($client);
            $this->getEntityManager()->persist($cart);
            $this->getEntityManager()->flush();
        }

        return $cart;
    }
}