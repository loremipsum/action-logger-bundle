<?php

namespace LoremIpsum\ActionLoggerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LoremIpsumActionLoggerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('loremipsum.action_logger.mapping', (! empty($config['mapping']) ? $config['mapping'] : []));
        $container->setParameter('loremipsum.action_logger.entity_mapping', (! empty($config['entity_mapping']) ? $config['entity_mapping'] : []));
    }
}
