<?php

namespace App\Command;

use App\Repository\Contact\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cleanup:contacts',
    description: 'Hard delete contacts',
)]
class CleanupContactsCommand extends Command
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly int $softDeleteRetentionDays
    ){
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $before = new \DateTimeImmutable(sprintf('-%d days', $this->softDeleteRetentionDays));

        $contacts = $this->contactRepository->findExpiredSoftDeleted($before);

        foreach($contacts as $contact){
            $this->entityManager->remove($contact);
        }

        $this->entityManager->flush();

       $output->writeln(sprintf('Deleted %d contacts', count($contacts)));

        return Command::SUCCESS;
    }
}
