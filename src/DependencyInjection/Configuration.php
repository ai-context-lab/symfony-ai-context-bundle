<?php

namespace AiContextBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ai_context');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('include')
                    ->addDefaultsIfNotSet()
                    ->children()
                    ->booleanNode('entities')->defaultTrue()->end()
                    ->end()
                ->end()

                ->scalarNode('output_dir')
                    ->defaultValue('%kernel.project_dir%/var/ai_context')
                ->end()

                ->scalarNode('output_filename')
                    ->defaultValue('ai-context.json')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
