<?php

namespace App\Command;

use App\Repository\Workspace\WorkspaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cleanup:workspaces',
    description: 'Hard delete workspaces',
)]
class CleanupWorkspacesCommand extends Command
{
    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly int $softDeleteRetentionDays
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $before = new \DateTimeImmutable(sprintf('-%d days', $this->softDeleteRetentionDays));

        $workspaces = $this->workspaceRepository->findExpiredSoftDeleted($before);

        foreach($workspaces as $workspace){
            $this->entityManager->remove($workspace);
        }

        $this->entityManager->flush();

       $output->writeln(sprintf('Deleted %d workspaces', count($workspaces)));

        return Command::SUCCESS;
    }
}
