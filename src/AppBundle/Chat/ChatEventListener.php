<?php

namespace AppBundle\Chat;

use Kraken\Network\NetworkConnectionInterface;
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
     * @var SplObjectStorage
     */
    private $conns;

    /**
     * @var array
     */
    private $users;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->conns = new SplObjectStorage();
        $this->users = [];
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
        $name  = $this->getUsername($conn);
        $color = '#' . dechex(rand(0x000000, 0xFFFFFF));
        $newUser = false;

        if (!isset($this->users[$name]))
        {
            $newUser = true;
            $this->users[$name] = [
                'data'  => [
                    'id'    => $id,
                    'name'  => $name,
                    'color' => $color,
                    'date'  => date('H:i:s')
                ],
                'conns' => new SplObjectStorage(),
            ];
        }

        if ($newUser)
        {
            $bubble = [
                'type' => 'connect',
                'data' => $this->users[$name]['data']
            ];
            $this->broadcast($bubble);
        }

        $conns = $this->users[$name]['conns'];
        $conns->attach($conn);

        $users = [];
        foreach ($this->users as $user)
        {
            $users[] = $user['data'];
        }

        $bubble['type'] = 'init';
        $bubble['data'] = $this->users[$name]['data'];
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
        $name = $this->getUsername($conn);

        $data  = $this->users[$name]['data'];
        $conns = $this->users[$name]['conns'];
        $conns->detach($conn);

        if (count($conns) === 0)
        {
            unset($this->users[$name]);

            $bubble = [
                'type' => 'disconnect',
                'data' => $data
            ];
            $this->broadcast($bubble);
        }
    }

    /**
     * Called whenever new message is written.
     *
     * @param ClientMessageEvent $event
     */
    public function onClientMessage(ClientMessageEvent $event)
    {
        $conn = $event->getConnection();
        $name = $this->getUsername($conn);
        $message = $event->getMessage();

        $data = json_decode($message->read(), true);
        $type = $data['type'];
        $data = $data['data'];

        if ($type === 'message')
        {
            $bubble = $this->users[$name]['data'];
            $bubble['mssg'] = $data;
            $bubble = [
                'type' => 'message',
                'data' => $bubble
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
//        $conn = $event->getConnection();
//        $conn->close(); // TODO fix it - crashes with "Maximum function nesting level of 'X' reached, aborting!"
    }

    /**
     * @param mixed[] $message
     */
    protected function broadcast($message)
    {
        foreach ($this->users as $user)
        {
            foreach ($user['conns'] as $conn)
            {
                $conn->send((string)json_encode($message));
            }
        }
    }

    private function getUsername(NetworkConnectionInterface $conn)
    {
        $token = $this->tokenStorage->getToken();

        $user = $token instanceof AnonymousToken ? 'anon-' . $conn->getResourceId() : $token->getUser();
        $username = $user instanceof UserInterface ? $user->getUsername() : $user;

        return $username;
    }
}
