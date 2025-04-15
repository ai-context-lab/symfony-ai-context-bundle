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
    public function __construct(
        private readonly ManagerRegistry $doctrine
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Génération du contexte IA (entités Doctrine)');

        $entityManager = $this->doctrine->getManager();
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();

        $result = [];

        foreach ($metaData as $classMetadata) {
            $entityName = $classMetadata->getName();
            $fields = [];
            foreach ($classMetadata->getFieldNames() as $field) {
                $fields[$field] = $classMetadata->getTypeOfField($field);
            }

            $associations = [];
            foreach ($classMetadata->getAssociationMappings() as $assocName => $assocData) {
                $type = match ($assocData['type']) {
                    1 => 'OneToOne',
                    2 => 'ManyToOne',
                    4 => 'OneToMany',
                    8 => 'ManyToMany',
                    default => 'Unknown',
                };
                $associations[$assocName] = $type . ' => ' . $assocData['targetEntity'];
            }

            $result[] = [
                'entity' => $entityName,
                'fields' => $fields,
                'associations' => $associations,
            ];
        }

        $outputDir = __DIR__ . '/../../var/ai_context';
        @mkdir($outputDir, 0777, true);

        file_put_contents(
            $outputDir . '/ai-context.json',
            json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $io->success('Fichier JSON généré avec succès dans var/ai_context/ai-context.json');

        return Command::SUCCESS;
    }
}
