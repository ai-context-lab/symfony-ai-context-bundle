<?php
namespace AiContextBundle\Generator;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class ServiceContextGenerator extends AbstractContextGenerator
{
    /**
     * @param array<int, string> $servicePaths
     */
    public function __construct(
        private readonly array $servicePaths
    ) {}

    public function generate(): array
    {
        $results = [];

        foreach ($this->findClasses($this->servicePaths) as $className) {
            $reflection = new ReflectionClass($className);

            if ($reflection->isAbstract() || $reflection->isInterface() || $reflection->isTrait()) {
                continue;
            }

            $results[] = [
                'class'   => $reflection->getName(),
                'short'   => $reflection->getShortName(),
                'methods' => array_map([$this, 'extractMethod'], $reflection->getMethods(ReflectionMethod::IS_PUBLIC)),
                'doc'     => $reflection->getDocComment(),
            ];
        }

        return $results;
    }

    /**
     * @return array{
     *     name: string,
     *     returnType: string,
     *     parameters: array<int, array{
     *         name: string,
     *         type: string,
     *         hasDefault: bool,
     *         default: mixed|null
     *     }>,
     *     doc: string|false
     * }
     */
    private function extractMethod(ReflectionMethod $method): array
    {
        return [
            'name'       => $method->getName(),
            'returnType' => $method->getReturnType()?->__toString() ?? 'mixed',
            'parameters' => array_map(
                fn(ReflectionParameter $param) => [
                    'name'       => $param->getName(),
                    'type'       => $param->getType()?->__toString() ?? 'mixed',
                    'hasDefault' => $param->isDefaultValueAvailable(),
                    'default'    => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                ],
                $method->getParameters()
            ),
            'doc' => $method->getDocComment(),
        ];
    }
}
