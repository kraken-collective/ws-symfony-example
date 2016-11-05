<?php
declare(strict_types = 1);
namespace KrakenCollective\WsSymfonyBundle\Command;

use Kraken\Ipc\Socket\SocketListener;
use Kraken\Loop\Loop;
use Kraken\Loop\Model\SelectLoop;
use Kraken\Network\Http\Component\Session\HttpSession;
use Kraken\Network\NetworkConnectionInterface;
use Kraken\Network\NetworkMessageInterface;
use Kraken\Network\NetworkServer;
use Kraken\Network\Websocket\WsServer;
use Kraken\Network\NetworkComponentInterface;
use Ratchet\Session\Storage\VirtualSessionStorage;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StartCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('kraken:ws:start');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $n = ini_get('session.name');
        $sessionHandler = $this->getContainer()->get('session.handler');
        //$sessionId = 'k47anlrap2vo0rlqsdn38kvv04';

        //$session = $this->startSession($sessionId, $sessionHandler);


        $loop = new Loop(
            new SelectLoop()
        );

        $listener = new SocketListener(
            'tcp://127.0.0.1:6080',
            $loop
        );

        $server = new NetworkServer($listener);

        $component = $this->getComponent();

        $ws = new WsServer(null, new HttpSession(null, $component, $sessionHandler));
        //$ws = new WsServer(null, $component);

        $server->addRoute('/test', $ws);

        $loop->start();
    }

    private function startSession($sessionId, $sessionHandler)
    {
        $serializeHandler = ini_get('session.serialize_handler');
        $serialClass = "\\Ratchet\\Session\\Serialize\\{$this->toClassCase($serializeHandler)}Handler";

        if (!class_exists($serialClass))
        {
            throw new RuntimeException('Unable to parse session serialize handler.');
        }

        $serializer = new $serialClass;

        $session = new Session(new VirtualSessionStorage($sessionHandler, $sessionId, $serializer));
        $session->start();

        return $session;
    }

    /**
     * @return NetworkComponentInterface
     */
    private function getComponent()
    {
        return $this->getContainer()->get('app.network_component.test');
    }

    /**
     * @param string $langDef Input to convert
     *
     * @return string
     */
    protected function toClassCase($langDef)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $langDef)));
    }
}
