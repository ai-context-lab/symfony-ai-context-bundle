<?php

namespace AiContextBundle\Command;

use AiContextBundle\Builder\ContextBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'ai-context:generate',
    description: 'Generates an AI-friendly context file from Doctrine entities',
)]
class GenerateAiContextCommand extends Command
{
    public function __construct(
        private readonly ContextBuilder        $contextBuilder,
        private readonly ParameterBagInterface $params
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $this->params->get('ai_context.output_dir');
        $filename = $this->params->get('ai_context.output_filename');
        $fullPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($filename, DIRECTORY_SEPARATOR);

        $io->writeln("Building AI context from your project...");

        $context = $this->contextBuilder->build();
        $isChecksumChanged = $this->contextBuilder->getIsChecksumChanged();

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        if (!is_dir($path)) {
            $io->writeln("<error>Failed to create output directory: $path </error>");
            return Command::FAILURE;
        }

        if (!is_writable($path)) {
            $io->writeln("<error>The output directory is not writable: $path</error>");
            return Command::FAILURE;
        }

        $json = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $io->writeln("<error>Failed to encode context as JSON: " . json_last_error_msg()."</error>");
            return Command::FAILURE;
        }

        file_put_contents($fullPath, $json);

        if ($isChecksumChanged) {
            $io->writeln("New changes detected.");
            $io->writeln("AI context successfully generated at: <info>$fullPath</info>");
        } else {
            $io->writeln("No changes detected. Same context reused at: $fullPath");
        }

        return Command::SUCCESS;
    }

}
