<?php

namespace LoremIpsum\ActionLoggerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('lorem_ipsum_action_logger');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('lorem_ipsum_action_logger');
        }

        $rootNode
            ->children()
                ->arrayNode('mapping')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('action')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->arrayNode('alias')
                                ->beforeNormalization()->castToArray()->end()
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('entity_mapping')
                    ->normalizeKeys(false)
                    ->scalarPrototype()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
