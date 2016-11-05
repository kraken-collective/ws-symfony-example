<?php
namespace KrakenCollective\WsSymfonyBundle\Command;

use Kraken\Ipc\Socket\SocketListener;
use Kraken\Loop\Loop;
use Kraken\Loop\Model\SelectLoop;
use Kraken\Network\Http\Component\Session\HttpSession;
use Kraken\Network\NetworkServer;
use Kraken\Network\Websocket\WsServer;
use Kraken\Network\NetworkComponentInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $sessionHandler = $this->getContainer()->get('session.handler');

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

        $server->addRoute('/test', $ws);

        $loop->start();
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
