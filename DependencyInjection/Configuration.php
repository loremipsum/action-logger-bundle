<?php

namespace LoremIpsum\ActionLoggerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $name        = 'lorem_ipsum_admin_lte';
        $treeBuilder = new TreeBuilder($name);
        $this->getRootNode($treeBuilder, $name)
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

    /**
     * @param TreeBuilder $treeBuilder
     * @param string      $name
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getRootNode(TreeBuilder $treeBuilder, string $name)
    {
        if (method_exists($treeBuilder, 'getRootNode')) {
            return $treeBuilder->getRootNode();
        }
        // BC layer for symfony/config 4.1 and older
        return $treeBuilder->root($name);
    }
}
