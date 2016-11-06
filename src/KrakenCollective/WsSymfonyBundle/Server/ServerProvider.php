<?php
namespace KrakenCollective\WsSymfonyBundle\Server;

use KrakenCollective\WsSymfonyBundle\Server\Provider\AbstractProvider;

class ServerProvider extends AbstractProvider
{
    /**
     * @param $alias
     *
     * @return object | Server
     */
    public function getServer($alias)
    {
        return $this->get($alias);
    }
}
