<?php

namespace AiContextBundle\Builder;

use AiContextBundle\Generator\ControllerContextGenerator;
use AiContextBundle\Generator\EntityContextGenerator;
use AiContextBundle\Generator\EventContextGenerator;
use AiContextBundle\Generator\FormContextGenerator;
use AiContextBundle\Generator\RepositoryContextGenerator;
use AiContextBundle\Generator\RouteContextGenerator;
use AiContextBundle\Generator\ServiceContextGenerator;
use AiContextBundle\Service\ChecksumService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ContextBuilder
{
    private bool $isChecksumChanged;

    public function __construct(
        private readonly EntityContextGenerator     $entityGenerator,
        private readonly RouteContextGenerator      $routeGenerator,
        private readonly ServiceContextGenerator    $serviceGenerator,
        private readonly ControllerContextGenerator $controllerGenerator,
        private readonly RepositoryContextGenerator $repositoryGenerator,
        private readonly EventContextGenerator      $eventGenerator,
        private readonly FormContextGenerator       $formContextGenerator,
        private readonly ChecksumService            $checksumService,
        private readonly ParameterBagInterface      $params
    ) {
    }

    /**
     * Generate the context for AI.
     * @return array<string, array<array<string, mixed>>>
     */
    public function build(): array
    {
        $entityPaths = $this->params->get('ai_context.paths.entities') ?? [];
        $servicePaths = $this->params->get('ai_context.paths.services') ?? [];
        $controllerPaths = $this->params->get('ai_context.paths.controllers') ?? [];
        $repositoryPaths = $this->params->get('ai_context.paths.repositories') ?? [];
        $eventPaths = $this->params->get('ai_context.paths.events') ?? [];
        $formPaths = $this->params->get('ai_context.paths.forms') ?? [];

        $allPaths = array_merge(
            (array)$entityPaths,
            (array)$servicePaths,
            (array)$controllerPaths,
            (array)$repositoryPaths,
            (array)$eventPaths,
            (array)$formPaths
        );

        if (!$this->checksumService->hasChanged($allPaths)) {
            $this->setIsChecksumChanged(false);
            $context = $this->loadLastContextFromFile();
            if (!empty($context)) {
                return $context;
            }
        }

        $this->setIsChecksumChanged(true);
        $context = [];

        if ($this->params->get('ai_context.include.controllers')) {
            $context['controllers'] = $this->controllerGenerator->generate();
        }

        if ($this->params->get('ai_context.include.entities')) {
            $context['entities'] = $this->entityGenerator->generate();
        }

        if ($this->params->get('ai_context.include.routes')) {
            $context['routes'] = $this->routeGenerator->generate();
        }

        if ($this->params->get('ai_context.include.repositories')) {
            $context['repositories'] = $this->repositoryGenerator->generate();
        }

        if ($this->params->get('ai_context.include.services')) {
            $context['services'] = $this->serviceGenerator->generate();
        }

        if ($this->params->get('ai_context.include.events')) {
            $context['events'] = $this->eventGenerator->generate();
        }

        if ($this->params->get('ai_context.include.forms')) {
            $context['forms'] = $this->formContextGenerator->generate();
        }

        $this->checksumService->saveChecksum($allPaths);

        return $context;
    }

    /**
     * @return array<string, array<array<string, mixed>>>
     */
    private function loadLastContextFromFile(): array
    {
        $outputDir = $this->params->get('ai_context.output_dir');
        $filename = $this->params->get('ai_context.output_filename');

        if (!is_string($outputDir) || !is_string($filename)) {
            return [];
        }

        $fullPath = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($filename, DIRECTORY_SEPARATOR);

        if (!file_exists($fullPath)) {
            return [];
        }

        $json = file_get_contents($fullPath);
        if ($json === false) {
            return [];
        }

        $context = json_decode($json, true);
        if ($context === null) {
            return [];
        }

        return $context;
    }

    /**
     * @return bool
     */
    public function getIsChecksumChanged()
    {
        return $this->isChecksumChanged;
    }

    /**
     * @param bool $isChecksumChanged
     */
    public function setIsChecksumChanged(bool $isChecksumChanged): void
    {
        $this->isChecksumChanged = $isChecksumChanged;
    }
}
