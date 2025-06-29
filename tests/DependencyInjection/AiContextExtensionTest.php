<?php

namespace AiContextBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use AiContextBundle\DependencyInjection\AiContextExtension;

class AiContextExtensionTest extends TestCase
{
    public function testFormContextGeneratorServiceIsRegisteredIfFormComponentPresent(): void
    {
        if (!interface_exists(\Symfony\Component\Form\FormFactoryInterface::class)) {
            $this->markTestSkipped('symfony/form is not installed');
        }

        $container = new ContainerBuilder();
        $extension = new AiContextExtension();

        $extension->load([[
            'include' => [
                'forms' => true,
                'entities' => false,
                'routes' => false,
                'services' => false,
                'controllers' => false,
                'repositories' => false,
                'events' => false,
            ],
            'paths' => [
                'forms' => [__DIR__ . '/../Fixtures/Form'],
                'entities' => [],
                'services' => [],
                'controllers' => [],
                'repositories' => [],
                'events' => [],
            ],
            'output_dir' => sys_get_temp_dir(),
            'output_filename' => 'context.json',
            'output_dir_checksum' => sys_get_temp_dir(),
        ]], $container);

        $this->assertTrue(
            $container->hasDefinition(\AiContextBundle\Generator\FormContextGenerator::class),
            'FormContextGenerator should be registered when symfony/form is available'
        );
    }
}
