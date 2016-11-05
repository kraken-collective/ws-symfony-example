<?php
namespace KrakenCollective\WsSymfonyBundle\Command;

use KrakenCollective\WsSymfonyBundle\DependencyInjection\KrakenCollectiveWsSymfonyExtension;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('kraken:ws:start')
            ->addArgument('server', InputArgument::REQUIRED, 'Name of the server to run.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO secure server name
        // TODO load service via alias
        $server = $this->getContainer()->get(
            sprintf(
                '%s.%s.%s',
                KrakenCollectiveWsSymfonyExtension::SERVICE_VENDOR_PREFIX,
                KrakenCollectiveWsSymfonyExtension::SERVER_SERVICE_ID_PREFIX,
                $input->getArgument('server')
            )
        );

        $server->start();
    }
}
