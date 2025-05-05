<?php

namespace AiContextBundle\Tests\Generator;

use AiContextBundle\Generator\RepositoryContextGenerator;
use PHPUnit\Framework\TestCase;

class RepositoryContextGeneratorTest extends TestCase
{
    public function testGenerateWithConcreteRepository(): void
    {
        $generator = new RepositoryContextGenerator([
            __DIR__ . '/fixtures/Repository'
        ]);

        $results = $generator->generate();

        /** @phpstan-ignore-next-line */
        $this->assertIsArray($results);
        $this->assertNotEmpty($results);

        $repository = $results[0];
        $this->assertArrayHasKey('class', $repository);
        $this->assertArrayHasKey('short', $repository);
        $this->assertArrayHasKey('methods', $repository);
        $this->assertIsArray($repository['methods']);

        $method = $repository['methods'][0];
        $this->assertArrayHasKey('name', $method);
        $this->assertArrayHasKey('parameters', $method);
        $this->assertArrayHasKey('returnType', $method);
    }
}
