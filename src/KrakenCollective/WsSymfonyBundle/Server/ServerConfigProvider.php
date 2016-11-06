<?php
namespace KrakenCollective\WsSymfonyBundle\Server;

use KrakenCollective\WsSymfonyBundle\Server\Provider\AbstractProvider;

class ServerConfigProvider extends AbstractProvider
{
    /**
     * @param $alias
     *
     * @return object | ServerConfig
     */
    public function getServerConfig($alias)
    {
        return $this->get($alias);
    }
}
