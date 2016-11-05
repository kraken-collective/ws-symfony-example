<?php

namespace KrakenCollective\WsSymfonyBundle\Event;

class ClientMessageEvent extends ClientEvent
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
