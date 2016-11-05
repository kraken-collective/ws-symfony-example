<?php

namespace KrakenCollective\WsSymfonyBundle\Server;

use Kraken\Network\NetworkComponentInterface;
use Kraken\Network\NetworkConnectionInterface;
use Kraken\Network\NetworkMessageInterface;
use KrakenCollective\WsSymfonyBundle\Event\ClientErrorEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientMessageEvent;
use KrakenCollective\WsSymfonyBundle\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServerDefaultComponent implements NetworkComponentInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleConnect(NetworkConnectionInterface $conn)
    {
        $event = new ClientEvent(ClientEvent::CONNECTED, $conn);
        $this->eventDispatcher->dispatch(Events::CLIENT_CONNECTED, $event);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleDisconnect(NetworkConnectionInterface $conn)
    {
        $event = new ClientEvent(ClientEvent::DISCONNECTED, $conn);
        $this->eventDispatcher->dispatch(Events::CLIENT_DISCONNECTED, $event);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleMessage(NetworkConnectionInterface $conn, NetworkMessageInterface $message)
    {
        $event = new ClientMessageEvent(ClientEvent::MESSAGE, $conn);
        $this->eventDispatcher->dispatch(Events::CLIENT_MESSAGE, $event);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleError(NetworkConnectionInterface $conn, $ex)
    {
        $event = new ClientErrorEvent(ClientEvent::ERROR, $conn);
        $this->eventDispatcher->dispatch(Events::CLIENT_ERROR, $event);
    }
}
