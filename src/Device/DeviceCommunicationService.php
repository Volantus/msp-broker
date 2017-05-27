<?php
namespace Volantus\MspBroker\Src\Device;

use Volantus\FlightBase\Src\General\MSP\MSPRequestFailedMessage;
use Volantus\FlightBase\Src\General\MSP\MSPRequestMessage;
use Volantus\FlightBase\Src\General\MSP\MSPResponseMessage;
use Volantus\MSPProtocol\Src\Protocol\CommunicationService;

/**
 * Class DeviceCommunicationService
 *
 * @package Volantus\MspBroker\Src\Device
 */
class DeviceCommunicationService
{
    /**
     * @var CommunicationService
     */
    private $serialService;

    /**
     * DeviceCommunicationService constructor.
     *
     * @param CommunicationService $serialService
     */
    public function __construct(CommunicationService $serialService = null)
    {
        $this->serialService = $serialService ?: new CommunicationService();
    }

    /**
     * @param MSPRequestMessage $requestMessage
     *
     * @return MSPResponseMessage|MSPRequestFailedMessage
     */
    public function handleRequest(MSPRequestMessage $requestMessage)
    {
        try {
            $response = $this->serialService->send($requestMessage->getMspRequest());
            return new MSPResponseMessage($requestMessage->getId(), $response);
        } catch (\Exception $e) {
            return new MSPRequestFailedMessage($requestMessage->getId());
        }
    }
}