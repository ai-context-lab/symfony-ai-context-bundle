<?php

namespace AiContextBundle\Generator;

use ReflectionClass;
use Symfony\Contracts\EventDispatcher\Event;

class EventContextGenerator extends AbstractContextGenerator
{
    public function __construct(
        /** @var string[] $eventPaths */
        private readonly array $eventPaths,
    ) {
    }

    public function generate(): array
    {
        /** @var class-string[] $classes */
        $classes = $this->findClasses($this->eventPaths);
        $events = [];

        foreach ($classes as $className) {
            $events[] = [
                'class'      => $className,
                'properties' => $this->extractClassProperties($className),
            ];
        }

        return $events;
    }

    /**
     * @param class-string $fqcn
     * @return array<string, string>
     */
    private function extractClassProperties(string $fqcn): array
    {
        $reflection = new ReflectionClass($fqcn);
        $props = [];

        foreach ($reflection->getProperties() as $prop) {
            $props[$prop->getName()] = (string)$prop->getType() ?: 'mixed';
        }

        return $props;
    }
}
