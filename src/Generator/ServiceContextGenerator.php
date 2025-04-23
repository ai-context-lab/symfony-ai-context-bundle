<?php

namespace AiContextBundle\Generator;

use Symfony\Component\Finder\Finder;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class ServiceContextGenerator
{
    public function __construct(
        private readonly array $servicePaths
    ) {
    }

    /**
     *
     * @return array
     */
    public function generate(): array
    {
        $finder = new Finder();
        $finder->files()->in($this->servicePaths)->name('*.php');

        $results = [];

        foreach ($finder as $file) {
            $className = $this->getClassFromFile($file->getRealPath());

            if (!$className || !class_exists($className)) {
                continue;
            }

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

    private function extractMethod(ReflectionMethod $method): array
    {
        return [
            'name'       => $method->getName(),
            'returnType' => $method->getReturnType()?->__toString() ?? 'mixed',
            'parameters' => array_map(fn(ReflectionParameter $param) => [
                'name'       => $param->getName(),
                'type'       => $param->getType()?->__toString() ?? 'mixed',
                'hasDefault' => $param->isDefaultValueAvailable(),
                'default'    => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
            ], $method->getParameters()),
            'doc'        => $method->getDocComment(),
        ];
    }

    private function getClassFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);

        if (!preg_match('/namespace\s+([^;]+);/', $contents, $namespaceMatch)) {
            return null;
        }

        if (!preg_match('/class\s+([^\s]+)/', $contents, $classMatch)) {
            return null;
        }

        return $namespaceMatch[1] . '\\' . $classMatch[1];
    }
}
