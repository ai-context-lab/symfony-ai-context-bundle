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
        $path = $this->params->get('ai_context.output_dir');
        $filename = $this->params->get('ai_context.output_filename');
        $fullPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($filename, DIRECTORY_SEPARATOR);

        $context = $this->contextBuilder->build();

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        if (!is_dir($path)) {
            $output->writeln("<error>Failed to create output directory: $path</error>");
            return Command::FAILURE;
        }

        if (!is_writable($path)) {
            $output->writeln("<error>The output directory is not writable: $path</error>");
            return Command::FAILURE;
        }

        $json = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $output->writeln("<error>Failed to encode context as JSON: " . json_last_error_msg() . "</error>");
            return Command::FAILURE;
        }

        if (file_exists($fullPath)) {
            $output->writeln("<comment>The file $fullPath already exists and will be overwritten.</comment>");
        }

        file_put_contents($fullPath, $json);

        $output->writeln("<info>AI context successfully generated at: $fullPath</info>");

        return Command::SUCCESS;
    }
}
