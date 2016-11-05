<?php
namespace KrakenCollective\WsSymfonyBundle\Server;

use Kraken\Loop\LoopExtendedInterface;
use Kraken\Network\NetworkServerInterface;
use Kraken\Network\Websocket\WsServerInterface;

class Server
{
    /** @var LoopExtendedInterface */
    private $loop;

    /** @var NetworkServerInterface */
    private $networkServer;

    public function __construct(LoopExtendedInterface $loop, NetworkServerInterface $networkServer)
    {
        $this->loop = $loop;
        $this->networkServer = $networkServer;
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
     * @return NetworkServerInterface
     */
    public function getNetworkServer()
    {
        return $this->networkServer;
    }
}
