<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixture extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {
        
    }
    
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setName('Admin');
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin,'123456@');
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);
        $manager->flush();

        $client = new Client();
        $client->setName('Karim');
        $client->setEmail('karim@example.com');
        $client->setRoles(['ROLE_CLIENT']);
        $client->setIsBanned(false);
        $hashedPassword = $this->passwordHasher->hashPassword($client,'123456@');
        $client->setPassword($hashedPassword);
        $manager->persist($client);
        $manager->flush();
    }
}
