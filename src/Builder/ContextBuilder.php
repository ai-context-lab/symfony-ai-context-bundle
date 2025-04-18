<?php
namespace AiContextBundle\Builder;

use AiContextBundle\Generator\EntityContextGenerator;
use AiContextBundle\Generator\RouteContextGenerator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ContextBuilder
{
    public function __construct(
        private readonly EntityContextGenerator $entityGenerator,
        private readonly RouteContextGenerator $routeGenerator,
        private readonly ParameterBagInterface $params
    ) {}

    /**
     * Generate the context for AI.
     * @return array
     */
    public function build(): array
    {
        $context = [];

        if ($this->params->get('ai_context.include.entities')) {
            $context['entities'] = $this->entityGenerator->generate();
        }

        if ($this->params->get('ai_context.include.routes')) {
            $context['routes'] = $this->routeGenerator->generate();
        }

        return $context;
    }
}
