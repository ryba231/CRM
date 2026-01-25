<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager) : void 
    {
        $system = new User();
        $system->setEmail('system@internal');
        $system->setFirstName('system');
        $system->setLastName('system');
        $system->setRoles(['ROLE_SYSTEM']);
        $system->setPassword($this->passwordHasher->hashPassword($system, 'pass'));
        $system->setIsActive(false);
        $manager->persist($system);

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstName('user');
        $user->setLastName('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'test1234'));
        $manager->persist($user);

        $manager->flush();
    }
}