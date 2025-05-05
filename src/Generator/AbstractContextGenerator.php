<?php

namespace AiContextBundle\Generator;

use Symfony\Component\Finder\Finder;

abstract class AbstractContextGenerator
{
    /**
     * @param array<int, string> $paths
     * @return array<int, class-string>
     */
    protected function findClasses(array $paths): array
    {
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php');

        $classes = [];

        foreach ($finder as $file) {
            $className = $this->getClassFromFile($file->getRealPath());

            if ($className && class_exists($className)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }


    protected function getClassFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath) ?: '';

        if (!preg_match('/namespace\s+([^;]+);/', $contents, $namespaceMatch)) {
            return null;
        }

        if (!preg_match('/class\s+([^\s]+)/', $contents, $classMatch)) {
            return null;
        }

        return $namespaceMatch[1] . '\\' . $classMatch[1];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    abstract public function generate(): array;
}
