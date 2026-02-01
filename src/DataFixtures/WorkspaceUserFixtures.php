<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\WorkspaceUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class WorkspaceUserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $owner = new User();
        $owner->setEmail('owner@example.com');
        $owner->setFirstName('owner');
        $owner->setLastName('owner');
        $owner->setRoles(['ROLE_USER']);
        $owner->setPassword($this->passwordHasher->hashPassword($owner, 'test1234'));
        $manager->persist($owner);

        $member = new User();
        $member->setEmail('member@example.com');
        $member->setFirstName('member');
        $member->setLastName('member');
        $member->setRoles(['ROLE_USER']);
        $member->setPassword($this->passwordHasher->hashPassword($member, 'test1234'));
        $manager->persist($member);

        $member = new User();
        $member->setEmail('add@example.com');
        $member->setFirstName('add');
        $member->setLastName('add');
        $member->setRoles(['ROLE_USER']);
        $member->setPassword($this->passwordHasher->hashPassword($member, 'test1234'));
        $manager->persist($member);

        $workspace = new Workspace();
        $workspace->setName("Workspace:");
        $manager->persist($workspace);

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setUser($owner);
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setRole('owner');
        $manager->persist($workspaceUser);

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setUser($member);
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setRole('member');
        $manager->persist($workspaceUser);

        $manager->flush();
    }
}