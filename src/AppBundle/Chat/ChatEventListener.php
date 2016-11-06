<?php

namespace AppBundle\Chat;

use KrakenCollective\WsSymfonyBundle\Event\ClientErrorEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientMessageEvent;
use SplObjectStorage;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatEventListener
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenProvider;

    /**
     * @var
     */
    private $conns;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
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

        $token = $this->tokenStorage->getToken();

        if ($token instanceof AnonymousToken) {
            $user = 'anonymous';
        } else {
            $user = $token->getUser();
        }

        $username = $user instanceof UserInterface ? $user->getUsername() : $user;

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
                    'mssg'  => sprintf('[%s] %s', $username, $data)
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
    {
        $conn = $event->getConnection();
        $conn->close(); // TODO fix it - crashes with "Maximum function nesting level of 'X' reached, aborting!"
    }

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
