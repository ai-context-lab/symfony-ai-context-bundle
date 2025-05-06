<?php

namespace AiContextBundle\Tests;

use AiContextBundle\Generator\EventContextGenerator;
use PHPUnit\Framework\TestCase;

class EventContextGeneratorTest extends TestCase
{
    public function testGenerateReturnsExpectedEventStructure(): void
    {
        $eventPath = __DIR__ . '/fixtures/Event';
        $generator = new EventContextGenerator([$eventPath]);

        $result = $generator->generate();

        /** @phpstan-ignore-next-line */
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $event) {
            $this->assertArrayHasKey('class', $event);
            $this->assertArrayHasKey('properties', $event);
            $this->assertIsArray($event['properties']);
            $this->assertStringContainsString('AiContextBundle\\', $event['class']);
        }
    }
}
