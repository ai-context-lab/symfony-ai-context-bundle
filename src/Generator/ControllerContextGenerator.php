<?php

namespace AiContextBundle\Generator;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ControllerContextGenerator extends AbstractContextGenerator
{
    /**
     * @param array<int, string> $controllerPaths
     */
    public function __construct(
        private readonly array $controllerPaths
    ) {
    }

    public function generate(): array
    {
        $controllers = [];

        foreach ($this->findClasses($this->controllerPaths) as $className) {
            $reflection = new ReflectionClass($className);

            if (
                !$reflection->isSubclassOf(AbstractController::class) ||
                $reflection->isAbstract() ||
                $reflection->isInterface() ||
                $reflection->isTrait()
            ) {
                continue;
            }

            $tmp = [
                'class'   => $reflection->getName(),
                'short'   => $reflection->getShortName(),
                'methods' => array_map([$this, 'extractMethod'], $reflection->getMethods(ReflectionMethod::IS_PUBLIC)),
                'doc'     => $reflection->getDocComment(),
            ];

            $isGrantedAttributes = $reflection->getAttributes(IsGranted::class);
            if (!empty($isGrantedAttributes)) {
                foreach ($isGrantedAttributes as $attribute) {
                    $isGranted = $attribute->newInstance();
                    $tmp['is_granted'] = [
                        'attribute' => $isGranted->attribute,
                        'subject'   => $isGranted->subject ?? null,
                    ];
                }
            }

            $controllers[] = $tmp;
        }

        return $controllers;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractMethod(ReflectionMethod $method): array
    {
        $methodData = [
            'name'       => $method->getName(),
            'parameters' => array_map(
                fn($param) => '$' . $param->getName() . ($param->hasType() ? ': ' . $param->getType() : ''),
                $method->getParameters()
            ),
            'doc'        => $method->getDocComment(),
        ];

        // Extracting route information
        $routeAttributes = $method->getAttributes("Symfony\Component\Routing\Attribute\Route");
        if (!empty($routeAttributes)) {
            $route = $routeAttributes[0]->newInstance();
            $methodData['route'] = [
                'path'    => $route->getPath(),
                'methods' => $route->getMethods(),
                'name'    => $route->getName(),
            ];
        }

        // Extracting is_granted information
        $isGrantedAttributes = $method->getAttributes(IsGranted::class);
        if (!empty($isGrantedAttributes)) {
            $isGranted = $isGrantedAttributes[0]->newInstance();
            $methodData['is_granted'] = [
                'attribute' => $isGranted->attribute,
                'subject'   => $isGranted->subject ?? null,
            ];
        }

        return $methodData;
    }
}
