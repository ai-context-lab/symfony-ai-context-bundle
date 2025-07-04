<?php

namespace AiContextBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;


class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ai_context');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('include')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('entities')->defaultTrue()->end()
                        ->booleanNode('routes')->defaultTrue()->end()
                        ->booleanNode('services')->defaultTrue()->end()
                        ->booleanNode('controllers')->defaultTrue()->end()
                        ->booleanNode('repositories')->defaultTrue()->end()
                        ->booleanNode('events')->defaultTrue()->end()
                        ->booleanNode('forms')->defaultTrue()->end()
                    ->end()
                ->end()

                ->arrayNode('paths')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('entities')
                                ->scalarPrototype()->end()
                                ->defaultValue(['src/Entity'])
                            ->end()
                        ->arrayNode('services')
                            ->scalarPrototype()->end()
                            ->defaultValue(['src/Service'])
                        ->end()
                        ->arrayNode('controllers')
                            ->scalarPrototype()->end()
                            ->defaultValue(['src/Controller'])
                        ->end()
                        ->arrayNode('repositories')
                            ->scalarPrototype()->end()
                            ->defaultValue(['src/Repository'])
                        ->end()
                        ->arrayNode('events')
                            ->scalarPrototype()->end()
                            ->defaultValue(['src/Event'])
                        ->end()
                        ->arrayNode('forms')
                          ->scalarPrototype()->end()
                          ->defaultValue(['src/Form'])
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('output_dir')
                    ->defaultValue('%kernel.project_dir%/var/ai_context')
                ->end()

                ->scalarNode('output_filename')
                    ->defaultValue('ai-context.json')
                ->end()

                ->scalarNode('output_dir_checksum')
                    ->defaultValue('%kernel.project_dir%/var/ai_context/ai-context-checksum.json')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
