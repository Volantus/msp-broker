<?php
namespace Volantus\MspBroker\Src\CLI;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Volantus\MspBroker\Src\Networking\ServerController;

/**
 * Class BrokerCommand
 *
 * @package Volantus\MspBroker\CLI
 */
class BrokerCommand extends Command
{
    protected function configure()
    {
        $this->setName('broker');
        $this->setDescription('Runs the SMP broker service');

        $this->addOption('port', 'p', InputArgument::OPTIONAL, 'Port of the webSocket', 9001);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = new ServerController($output);

        $server = IoServer::factory(new HttpServer(new WsServer($controller)), $input->getOption('port'));
        $server->run();
    }
}