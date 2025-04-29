<?php

namespace AiContextBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class AiContextExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ai_context.include.entities', $config['include']['entities']);
        $container->setParameter('ai_context.include.routes', $config['include']['routes']);
        $container->setParameter('ai_context.include.services', $config['include']['services']);
        $container->setParameter('ai_context.include.controllers', $config['include']['controllers']);
        $container->setParameter('ai_context.paths.entities', $config['paths']['entities']);
        $container->setParameter('ai_context.paths.services', $config['paths']['services']);
        $container->setParameter('ai_context.paths.controllers', $config['paths']['controllers']);
        $container->setParameter('ai_context.output_dir', $config['output_dir']);
        $container->setParameter('ai_context.output_filename', $config['output_filename']);
        $container->setParameter('ai_context.output_dir_checksum', $config['output_dir_checksum']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
    }
}
