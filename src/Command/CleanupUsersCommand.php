<?php

namespace App\Command;

use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cleanup:users',
    description: 'Hard delete users',
)]
class CleanupUsersCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly int $softDeleteRetentionDays
    ) {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $before = new \DateTimeImmutable(sprintf('-%d days', $this->softDeleteRetentionDays));

        $users = $this->userRepository->findExpiredSoftDeleted($before);

        foreach($users as $user){
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();

       $output->writeln(sprintf('Deleted %d users', count($users)));

        return Command::SUCCESS;
    }
}
