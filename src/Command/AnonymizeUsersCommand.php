<?php

namespace App\Command;

use App\Repository\User\UserRepository;
use App\Service\User\UserAnonymizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:anonymize:users',
    description: 'Anonymize users',
)]
class AnonymizeUsersCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserAnonymizer $anonymizer,
        private readonly int $softDeleteRetentionDays
    ) {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $before = new \DateTimeImmutable(sprintf('-%d days', $this->softDeleteRetentionDays));
        $systemUser = $this->userRepository->getSystemUser();
        
        $users = $this->userRepository->findExpiredSoftDeleted($before);

        foreach($users as $user){
            $this->anonymizer->anonymize($user, $systemUser);
        }

       $output->writeln(sprintf('Deleted %d users', count($users)));

        return Command::SUCCESS;
    }
}
