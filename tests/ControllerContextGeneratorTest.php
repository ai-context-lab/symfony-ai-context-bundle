<?php

namespace AiContextBundle\Tests\Generator;

use PHPUnit\Framework\TestCase;
use AiContextBundle\Generator\ControllerContextGenerator;

class ControllerContextGeneratorTest extends TestCase
{
    public function testGenerateReturnsValidControllerData()
    {
        $generator = new ControllerContextGenerator([
            __DIR__ . '/fixtures/Controller'
        ]);

        $controllers = $generator->generate();

        $this->assertIsArray($controllers);
        $this->assertNotEmpty($controllers);

        $controller = $controllers[0];
        $this->assertArrayHasKey('class', $controller);
        $this->assertArrayHasKey('short', $controller);
        $this->assertArrayHasKey('methods', $controller);
        $this->assertIsArray($controller['methods']);

        $method = $controller['methods'][0];
        $this->assertArrayHasKey('name', $method);
        $this->assertArrayHasKey('parameters', $method);
        $this->assertArrayHasKey('doc', $method);
    }
}
