<?php

namespace AiContextBundle\Generator;

use Symfony\Component\Routing\RouterInterface;

class RouteContextGenerator
{
    public function __construct(private readonly RouterInterface $router) {}

    /**
     * Generate the context for AI.
     * @return array
     */
    public function generate(): array
    {
        $routes = $this->router->getRouteCollection();
        $context = [];

        foreach ($routes as $name => $route) {
            $defaults = $route->getDefaults();

            $context[] = [
                'name'       => $name,
                'path'       => $route->getPath(),
                'methods'    => $route->getMethods(),
                'controller' => $defaults['_controller'] ?? null,
                'defaults'   => array_filter($defaults, fn($key) => $key !== '_controller', ARRAY_FILTER_USE_KEY),
                'requirements' => $route->getRequirements(),
            ];
        }

        return $context;
    }
}
