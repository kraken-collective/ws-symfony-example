<?php

namespace KrakenCollective\WsSymfonyBundle\DependencyInjection;

use KrakenCollective\WsSymfonyBundle\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class KrakenCollectiveWsSymfonyExtension extends ConfigurableExtension
{
    const KEY_SOCKET_LISTENER = 'socket_listener';

    const TAG_LOOP_MODEL = 'kraken.loop_model';

    const PARAMETER_LOOP_CLASS = 'kraken_collective_ws_symfony_loop';
    const PARAMETER_SELECT_LOOP_CLASS = 'kraken_collective_ws_symfony_select_loop';
    const PARAMETER_SOCKET_LISTENER_CLASS = 'kraken_collective_ws_symfony_socket_listener';

    const LOOP_SERVICE_ID_PREFIX = 'kraken.ws.loop';
    const LOOP_MODEL_SERVICE_ID_PREFIX = 'kraken.ws.loop_model';
    const SOCKET_LISTENER_SERVICE_ID_PREFIX = 'kraken.ws.socket_listener';

    private $loopModelsMap = [];

    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('services.yml');

        $this->loadLoops($container);
        $this->loadSocketListeners($container, $mergedConfig[self::KEY_SOCKET_LISTENER]);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function loadLoops(ContainerBuilder $container)
    {
        $loopModelsIds = $container->findTaggedServiceIds(self::TAG_LOOP_MODEL);

        foreach ($loopModelsIds as $loopModelId => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['alias'])) {
                    $this->registerLoop(
                        $container,
                        $container->getParameter(self::PARAMETER_LOOP_CLASS),
                        $tag['alias'],
                        $container->getDefinition($loopModelId)
                    );
                }
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $loopClass
     * @param string           $loopAlias
     * @param Definition       $loopModelDefinition
     */
    private function registerLoop(
        ContainerBuilder $container,
        $loopClass,
        $loopAlias,
        Definition $loopModelDefinition
    ) {
        $definition = new Definition($loopClass);
        $definition->addArgument($loopModelDefinition);

        $container->setDefinition(
            $this->getLoopServiceId($loopAlias),
            $definition
        );
    }

    /**
     * @param string $loopAlias
     * @return string
     */
    private function getLoopServiceId($loopAlias)
    {
        return sprintf('%s.%s', self::LOOP_SERVICE_ID_PREFIX, $loopAlias);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $configs
     *
     * @return void
     */
    private function loadSocketListeners(ContainerBuilder $container, array $configs)
    {
        foreach ($configs as $socketListenerName => $config) {
            $this->registerSocketListener($container, $socketListenerName, $config);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $socketListenerName
     * @param array            $config
     *
     * @return void
     */
    private function registerSocketListener(ContainerBuilder $container, $socketListenerName, array $config)
    {
        $socketListenerClass = $container->getParameter(self::PARAMETER_SOCKET_LISTENER_CLASS);

        $definition = new Definition($socketListenerClass);

        $definition->addArgument(sprintf('%s://%s:%s', $config['protocol'], $config['address'], $config['port']));
        $definition->addArgument($this->getLoopDefinition($container, $config['loop']));

        $container->setDefinition(
            sprintf('%s.%s', self::SOCKET_LISTENER_SERVICE_ID_PREFIX, $socketListenerName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $loopAlias
     * @return Definition
     */
    private function getLoopDefinition(ContainerBuilder $container, $loopAlias)
    {
        try {
            return $container->getDefinition($this->getLoopServiceId($loopAlias));
        } catch (ServiceNotFoundException $e) {
            throw new RuntimeException(sprintf('LoopModel service aliased "%s" was not found.', $loopAlias));
        }
    }
}
