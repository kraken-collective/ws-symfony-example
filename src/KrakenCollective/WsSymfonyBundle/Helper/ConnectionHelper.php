<?php
namespace KrakenCollective\WsSymfonyBundle\Helper;

use KrakenCollective\WsSymfonyBundle\Server\Server;

class ConnectionHelper
{
    /**
     * @param Server $server
     * @param string $token In most cases - session ID
     * @param string $route
     * @param string $protocol
     *
     * @return string
     */
    public function buildWebsocketAddress(Server $server, $token, $route = '/', $protocol = 'ws')
    {
        return sprintf('%s://%s%s?token=%s', $protocol, $server->getLocalAddress(), $route, $token);
    }
}
