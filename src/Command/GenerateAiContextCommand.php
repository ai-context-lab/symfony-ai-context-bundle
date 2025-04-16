<?php

namespace AiContextBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ai-context:generate',
    description: 'Génère un fichier de contexte IA à partir des entités Doctrine',
)]
class GenerateAiContextCommand extends Command
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Commande exécutée');
        return Command::SUCCESS;
    }
}
