<?php
namespace Volantus\MspBroker\Src\Networking;

use Symfony\Component\Console\Output\OutputInterface;
use Volantus\FlightBase\Src\General\Generic\GenericInternalMessage;
use Volantus\FlightBase\Src\General\Generic\IncomingGenericInternalMessage;
use Volantus\FlightBase\Src\General\MSP\MSPRequestMessage;
use Volantus\FlightBase\Src\Server\Messaging\MessageServerService;
use Volantus\FlightBase\Src\Server\Messaging\MessageService;
use Volantus\FlightBase\Src\Server\Network\ClientFactory;
use Volantus\MspBroker\Src\Device\DeviceCommunicationService;

/**
 * Class MessageHandler
 * @package Volantus\MspBroker\Networking
 */
class MessageHandler extends MessageServerService
{
    /**
     * @var DeviceCommunicationService
     */
    private $communicationService;

    /**
     * MessageHandler constructor.
     *
     * @param OutputInterface                 $output
     * @param MessageService|null             $messageService
     * @param ClientFactory|null              $clientFactory
     * @param DeviceCommunicationService|null $deviceCommunicationService
     */
    public function __construct(OutputInterface $output, MessageService $messageService = null, ClientFactory $clientFactory = null, DeviceCommunicationService $deviceCommunicationService = null)
    {
        parent::__construct($output, $messageService, $clientFactory);
        $this->communicationService = $deviceCommunicationService ?: new DeviceCommunicationService();
    }

    /**
     * @param IncomingGenericInternalMessage $message
     */
    public function handleGenericMessage(IncomingGenericInternalMessage $message)
    {
        if ($message->getPayload() instanceof MSPRequestMessage) {
            $response = $this->communicationService->handleRequest($message->getPayload());
            $response = new GenericInternalMessage($response);
            $response = $response->toRawMessage();
            $message->getSender()->send(json_encode($response));
        }
    }
}