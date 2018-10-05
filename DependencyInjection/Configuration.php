<?php

namespace LoremIpsum\ActionLoggerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('lorem_ipsum_action_logger');

        $rootNode
            ->children()
                ->arrayNode('mapping')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('action')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('class')
                                ->isRequired()
                            ->end()
                            ->arrayNode('alias')->beforeNormalization()->castToArray()->end()
        ;

        return $treeBuilder;
    }
}
