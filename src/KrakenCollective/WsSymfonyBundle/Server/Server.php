<?php
namespace KrakenCollective\WsSymfonyBundle\Server;

use Kraken\Loop\LoopExtendedInterface;
use Kraken\Network\Websocket\WsServerInterface;

class Server
{
    /** @var LoopExtendedInterface */
    private $loop;

    /** @var WsServerInterface */
    private $wsServer;

    public function __construct(LoopExtendedInterface $loop, WsServerInterface $wsServer)
    {
        $this->loop = $loop;
        $this->wsServer = $wsServer;
    }

    /**
     *
     */
    public function start()
    {
        $this->loop->start();
    }

    /**
     *
     */
    public function stop()
    {
        $this->loop->stop();
    }

    /**
     * @return LoopExtendedInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * @return WsServerInterface
     */
    public function getWsServer()
    {
        return $this->wsServer;
    }
}
