<?php

namespace AppBundle\Messages;

use KrakenCollective\WsSymfonyBundle\Event\ClientErrorEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientMessageEvent;

class ClientEventListener
{
    /**
     * Called whenever a client connects.
     *
     * @param ClientEvent $event
     */
    public function onClientConnect(ClientEvent $event)
    {
         // do something
    }

    /**
     * Called whenever a client disconnects.
     *
     * @param ClientEvent $event
     */
    public function onClientDisconnect(ClientEvent $event)
    {
        // do something
    }

    /**
     * Called whenever a client errors.
     *
     * @param ClientErrorEvent $event
     */
    public function onClientError(ClientErrorEvent $event)
    {
        // do something
    }

    /**
     * Called whenever new message is written.
     *
     * @param ClientMessageEvent $event
     */
    public function onClientMessage(ClientMessageEvent $event)
    {
        // do something
    }
}
