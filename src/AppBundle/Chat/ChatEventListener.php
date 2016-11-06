<?php

namespace AppBundle\Chat;

use KrakenCollective\WsSymfonyBundle\Event\ClientErrorEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientMessageEvent;
use SplObjectStorage;

class ChatEventListener
{
    /**
     * @var
     */
    private $conns;

    /**
     *
     */
    public function __construct()
    {
        $this->conns = new SplObjectStorage();
    }

    /**
     * Called whenever a client connects.
     *
     * @param ClientEvent $event
     */
    public function onClientConnect(ClientEvent $event)
    {
        $conn  = $event->getConnection();
        $id    = $conn->getResourceId();
        $name  = 'User #' . $id;
        $color = '#' . dechex(rand(0x000000, 0xFFFFFF));

        $bubble = [
            'type'  => 'connect',
            'data'  => [
                'id'    => $id,
                'name'  => $name,
                'color' => $color,
                'date'  => date('H:i:s')
            ]
        ];
        $this->broadcast($bubble);

        $conn->data = [
            'id'    => $id,
            'name'  => $name,
            'color' => $color
        ];
        $this->conns->attach($conn);

        $users = [];
        foreach ($this->conns as $conn)
        {
            $users[] = [
                'id'    => $conn->data['id'],
                'name'  => $conn->data['name'],
                'color' => $conn->data['color']
            ];
        }

        $bubble['type'] = 'init';
        $bubble['data']['users'] = $users;

        $conn->send((string)json_encode($bubble));
    }

    /**
     * Called whenever a client disconnects.
     *
     * @param ClientEvent $event
     */
    public function onClientDisconnect(ClientEvent $event)
    {
        $conn = $event->getConnection();
        $this->conns->detach($conn);

        $bubble = [
            'type'  => 'disconnect',
            'data'  => [
                'id'    => $conn->data['id'],
                'name'  => $conn->data['name'],
                'color' => $conn->data['color'],
                'date'  => date('H:i:s')
            ]
        ];
        $this->broadcast($bubble);
    }

    /**
     * Called whenever new message is written.
     *
     * @param ClientMessageEvent $event
     */
    public function onClientMessage(ClientMessageEvent $event)
    {
        $conn = $event->getConnection();
        $message = $event->getMessage();

        $data = json_decode($message->read(), true);
        $type = $data['type'];
        $data = $data['data'];

        if ($type === 'message')
        {
            $bubble = [
                'type' => 'message',
                'data' => [
                    'id'    => $conn->data['id'],
                    'name'  => $conn->data['name'],
                    'color' => $conn->data['color'],
                    'date'  => date('H:i:s'),
                    'mssg'  => $data
                ]
            ];
            return $this->broadcast($bubble);
        }
    }

    /**
     * Called whenever a client errors.
     *
     * @param ClientErrorEvent $event
     */
    public function onClientError(ClientErrorEvent $event)
    {}

    /**
     * @param mixed[] $message
     */
    protected function broadcast($message)
    {
        foreach ($this->conns as $conn)
        {
            $conn->send((string) json_encode($message));
        }
    }
}
