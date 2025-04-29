<?php


namespace AiContextBundle\Generator;

use Symfony\Component\Finder\Finder;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class RepositoryContextGenerator extends AbstractContextGenerator
{
    public function __construct(
        private readonly array $repositoryPaths
    ) {
    }

    public function generate(): array
    {
        $finder = new Finder();
        $finder->files()->in($this->repositoryPaths)->name('*.php');

        $repositories = [];

        foreach ($finder as $file) {
            $className = $this->getClassFromFile($file->getRealPath());

            if (!$className || !class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);

            // On veut seulement les Repository concrets
            if (
                !$reflection->isSubclassOf(ServiceEntityRepository::class) ||
                $reflection->isAbstract() ||
                $reflection->isInterface() ||
                $reflection->isTrait()
            ) {
                continue;
            }

            $repositories[] = [
                'class'   => $reflection->getName(),
                'short'   => $reflection->getShortName(),
                'methods' => array_map([$this, 'extractMethod'], $reflection->getMethods(ReflectionMethod::IS_PUBLIC)),
                'doc'     => $reflection->getDocComment(),
            ];
        }

        return $repositories;
    }

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
            'doc'        => $method->getDocComment(),
        ];
    }
}
