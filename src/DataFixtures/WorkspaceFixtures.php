<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\WorkspaceUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class WorkspaceFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $email = 'user.workspace@example.com';
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName('workspace');
        $user->setLastName('workspace');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'test1234'));
        $manager->persist($user);

        $workspace = new Workspace();
        $workspace->setName("Workspace: " . $user->getEmail());
        $manager->persist($workspace);

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setUser($user);
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setRole('owner');
        $manager->persist($workspaceUser);
        
        $workspace = new Workspace();
        $workspace->setName("Workspace: " . $user->getEmail());
        $manager->persist($workspace);

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setUser($user);
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setRole('member');
        $manager->persist($workspaceUser);

        $workspace = new Workspace();
        $workspace->setName("Workspace_delete: " . $user->getEmail());
        $workspace->setDeletedAt(new \DateTimeImmutable());
        $manager->persist($workspace);

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setUser($user);
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setRole('owner');
        $manager->persist($workspaceUser);

        $workspace = new Workspace();
        $workspace->setName("Workspace_member: " . $user->getEmail());
        $workspace->setDeletedAt(new \DateTimeImmutable());
        $manager->persist($workspace);

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setUser($user);
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setRole('member');
        $manager->persist($workspaceUser);

        $manager->flush(); 
    }
}