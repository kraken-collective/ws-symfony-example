<?php

namespace KrakenCollective\WsSymfonyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kraken_collective_ws_symfony');

        $rootNode
            ->append($this->getSocketListenerNode())
        ;

        return $treeBuilder;
    }

    private function getSocketListenerNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('socket_listener');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('protocol')->defaultValue('tcp')->end()
                    ->scalarNode('address')->isRequired()->end()
                    ->integerNode('port')->isRequired()->end()
                    ->scalarNode('loop')->defaultValue('select_loop')->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
