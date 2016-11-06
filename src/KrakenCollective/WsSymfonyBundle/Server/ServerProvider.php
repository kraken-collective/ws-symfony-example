<?php
namespace KrakenCollective\WsSymfonyBundle\Server;

use KrakenCollective\WsSymfonyBundle\Exception\RuntimeException;
use KrakenCollective\WsSymfonyBundle\Exception\ServerNotFoundException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ServerProvider
{
    use ContainerAwareTrait;

    /** @var string[] */
    private $servers = [];

    /**
     * @param string $serverAlias
     *
     * @return Server
     */
    public function getServer($serverAlias)
    {
        $this->guardAgainstMissingAlias($serverAlias);

        return $this->returnServer($serverAlias);
    }

    /**
     * @param string $serverAlias
     *
     * @return void
     *
     * @throws ServerNotFoundException
     */
    private function guardAgainstMissingAlias($serverAlias)
    {
        if (!isset($this->servers[$serverAlias])) {
            throw new ServerNotFoundException($serverAlias);
        }
    }

    /**
     * @param string $serverAlias
     *
     * @return Server | object
     */
    private function returnServer($serverAlias)
    {
        return $this->container->get(
            $this->servers[$serverAlias]
        );
    }

    /**
     * @param string $serverAlias
     * @param string $serverId
     *
     * @return void
     */
    public function registerServer($serverAlias, $serverId)
    {
        $this->guardAgainstDuplicatedServerAlias($serverAlias);
        $this->addServer($serverAlias, $serverId);
    }

    /**
     * @param string $serverAlias
     *
     * @return void
     *
     * @throws RuntimeException
     */
    private function guardAgainstDuplicatedServerAlias($serverAlias)
    {
        if (isset($this->servers[$serverAlias])) {
            throw new RuntimeException(
                sprintf(
                    'Server aliased "%s" ("%") has already been registered. You cannot have multiple servers with the same alias!',
                    $serverAlias,
                    $this->servers[$serverAlias]
                )
            );
        }
    }

    /**
     * @param string $serverAlias
     * @param string $serverId
     *
     * @return void
     */
    private function addServer($serverAlias, $serverId)
    {
        $this->servers[$serverAlias] = $serverId;
    }
}
