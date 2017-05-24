<?php
namespace Volantus\MspBroker\Src\Networking;

use Symfony\Component\Console\Output\OutputInterface;
use Volantus\FlightBase\Src\Server\Controller;
use Volantus\FlightBase\Src\Server\Messaging\MessageServerService;

/**
 * Class ServerController
 * @package Volantus\MspBroker\Networking
 */
class ServerController extends Controller
{
    /**
     * ServerController constructor.
     *
     * @param OutputInterface      $output
     * @param MessageServerService $messageRelayService
     */
    public function __construct(OutputInterface $output, MessageServerService $messageRelayService = null)
    {
        parent::__construct($output, $messageRelayService ?: new MessageHandler($output));
    }
}