<?php
namespace AiContextBundle\Builder;

use AiContextBundle\Generator\ControllerContextGenerator;
use AiContextBundle\Generator\EntityContextGenerator;
use AiContextBundle\Generator\RouteContextGenerator;
use AiContextBundle\Generator\ServiceContextGenerator;
use AiContextBundle\Service\ChecksumService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ContextBuilder
{
    private $isChecksumChanged;
    public function __construct(
        private readonly EntityContextGenerator $entityGenerator,
        private readonly RouteContextGenerator $routeGenerator,
        private readonly ServiceContextGenerator $serviceGenerator,
        private readonly ControllerContextGenerator $controllerGenerator,
        private readonly ChecksumService $checksumService,
        private readonly RouterInterface $router,
        private readonly ParameterBagInterface $params
    ) {}

    /**
     * Generate the context for AI.
     * @return array
     */
    public function build(): array
    {
        $entityPaths = $this->params->get('ai_context.paths.entities') ?? [];
        $servicePaths = $this->params->get('ai_context.paths.services') ?? [];
        $controllerPaths = $this->params->get('ai_context.paths.controllers') ?? [];
        $allPaths = array_merge($entityPaths, $servicePaths, $controllerPaths);

        if (!$this->checksumService->hasChanged($allPaths)) {
            $this->setIsChecksumChanged(false);
            return $this->loadLastContextFromFile();
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

        if ($this->params->get('ai_context.include.services')) {
            $context['services'] = $this->serviceGenerator->generate();
        }

        $this->checksumService->saveChecksum($allPaths);

        return $context;
    }

    private function loadLastContextFromFile()
    {
        $outputDir = $this->params->get('ai_context.output_dir');
        $filename = $this->params->get('ai_context.output_filename');
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
     * @return mixed
     */
    public function getIsChecksumChanged()
    {
        return $this->isChecksumChanged;
    }

    /**
     * @param mixed $isChecksumChanged
     */
    public function setIsChecksumChanged(bool $isChecksumChanged): void
    {
        $this->isChecksumChanged = $isChecksumChanged;
    }
}
