<?php

namespace App\Command;

use App\Repository\RevokedTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clean-revoked-tokens',
    description: 'Supprime les tokens révoqués expirés'
)]
class CleanRevokedTokensCommand extends Command
{
    public function __construct(
        private RevokedTokenRepository $revokedTokenRepository,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->revokedTokenRepository->cleanExpired();

        $io->success(sprintf(
            '%d token(s) révoqué(s) expiré(s) ont été supprimés.',
            $result
        ));

        return Command::SUCCESS;
    }
}
