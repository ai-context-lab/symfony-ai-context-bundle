<?php

namespace AiContextBundle\Tests\Generator;

use AiContextBundle\Generator\ServiceContextGenerator;
use PHPUnit\Framework\TestCase;

class ServiceContextGeneratorTest extends TestCase
{
    public function testGenerateWithConcreteService()
    {
        $generator = new ServiceContextGenerator([
            __DIR__ . '/fixtures/Service'
        ]);

        $results = $generator->generate();

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);

        $service = $results[0];
        $this->assertArrayHasKey('class', $service);
        $this->assertArrayHasKey('short', $service);
        $this->assertArrayHasKey('methods', $service);
        $this->assertIsArray($service['methods']);

        $method = $service['methods'][0];
        $this->assertArrayHasKey('name', $method);
        $this->assertArrayHasKey('parameters', $method);
        $this->assertArrayHasKey('returnType', $method);
    }
}
