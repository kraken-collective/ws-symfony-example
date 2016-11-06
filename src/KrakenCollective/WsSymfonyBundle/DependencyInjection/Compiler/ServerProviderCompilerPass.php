<?php

namespace KrakenCollective\WsSymfonyBundle\DependencyInjection\Compiler;

use KrakenCollective\WsSymfonyBundle\DependencyInjection\KrakenCollectiveWsSymfonyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ServerProviderCompilerPass implements CompilerPassInterface
{
    const PARAMETER_SERVICE_PROVIDER_ID = 'kraken.ws.server_provider';
    const METHOD_REGISTER_SERVER = 'registerServer';

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $serverProviderDefinition = $this->getServerProviderDefinition($container);
            $this->registerServersInProvider($container, $serverProviderDefinition);
        } catch (ServiceNotFoundException $e) {
            return;
        }
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return Definition
     */
    private function getServerProviderDefinition(ContainerBuilder $container)
    {
        return $container->getDefinition(self::PARAMETER_SERVICE_PROVIDER_ID);
    }

    /**
     * @param ContainerBuilder $container
     * @param Definition       $serverProviderDefinition
     *
     * @return void
     */
    private function registerServersInProvider(ContainerBuilder $container, Definition $serverProviderDefinition)
    {
        $serverIds = $container->findTaggedServiceIds(KrakenCollectiveWsSymfonyExtension::TAG_SERVER);

        foreach ($serverIds as $serverId => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['alias'])) {
                    $serverProviderDefinition->addMethodCall(
                        self::METHOD_REGISTER_SERVER,
                        [$tag['alias'], $serverId]
                    );
                }
            }
        }
    }
}
