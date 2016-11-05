<?php
namespace AppBundle\Service\NetworkComponent;

use Kraken\Network\NetworkComponentInterface;
use Kraken\Network\NetworkConnectionInterface;
use Kraken\Network\NetworkMessageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TestNetworkComponent implements NetworkComponentInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function handleConnect(NetworkConnectionInterface $conn)
    {
        $i=1;
    }

    public function handleMessage(NetworkConnectionInterface $conn, NetworkMessageInterface $message)
    {
        $this->setToken($this->getSession($conn));

        $response = 'response';

        $conn->send((string) $response);
        $conn->close();
    }

    public function handleDisconnect(NetworkConnectionInterface $conn)
    {
        $i=1;
    }

    public function handleError(NetworkConnectionInterface $conn, $ex)
    {
        $i=1;
    }

    /**
     * @param NetworkConnectionInterface $conn
     *
     * @return SessionInterface
     */
    private function getSession(NetworkConnectionInterface $conn)
    {
        return $conn->Session;
    }

    /**
     * @param SessionInterface $session
     */
    private function setToken(SessionInterface $session)
    {
        $token = unserialize($session->get('_security_main'));
        $this->tokenStorage->setToken($token);
    }
}
