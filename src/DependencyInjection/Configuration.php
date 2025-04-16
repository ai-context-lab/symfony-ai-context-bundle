<?php

namespace AiContextBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ai_context');

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('include')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('entities')->defaultTrue()->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
