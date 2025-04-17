<?php

namespace AiContextBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use AiContextBundle\Generator\EntityContextGenerator;

#[AsCommand(
    name: 'ai-context:generate',
    description: 'Génère un fichier de contexte IA à partir des entités Doctrine',
)]
class GenerateAiContextCommand extends Command
{
    public function __construct(
        private readonly EntityContextGenerator $entityContextGenerator,
        private readonly ParameterBagInterface $params
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $includeEntities = $this->params->get('ai_context.include.entities');
        $path = $this->params->get('ai_context.output_dir');
        $filename = $this->params->get('ai_context.output_filename');

        if ($includeEntities) {
            $data = $this->entityContextGenerator->generate();
            file_put_contents($path.$filename, json_encode($data, JSON_PRETTY_PRINT));
        }

        return Command::SUCCESS;
    }
}
