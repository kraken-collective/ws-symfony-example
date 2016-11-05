<?php
namespace KrakenCollective\WsSymfonyBundle\Command;

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
        $server = $this->getContainer()->get('kraken.ws.server.test_server');

        $server->start();
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
