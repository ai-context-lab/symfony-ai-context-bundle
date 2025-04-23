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

        $io->section('Generating AI context...');

        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                $io->error("Failed to create output directory: $path");
                return Command::FAILURE;
            }
        }

        if (!is_writable($path)) {
            $io->error("Output directory is not writable: $path");
            return Command::FAILURE;
        }

        $context = $this->contextBuilder->build();
        $isChecksumChanged = $this->contextBuilder->getIsChecksumChanged();

        $json = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $io->error("Failed to encode context as JSON: " . json_last_error_msg());
            return Command::FAILURE;
        }

        if ($isChecksumChanged) {
            if (file_exists($fullPath)) {
                $io->warning("The file already exists and will be overwritten.");
            }

            file_put_contents($fullPath, $json);
            $io->success("AI context successfully generated at:");
            $io->writeln(" → $fullPath");
        } else {
            $io->note("No changes detected. Context file not updated.");
        }

        return Command::SUCCESS;
    }

}
