<?php
namespace Volantus\MspBroker\Tests\Networking;

use Volantus\FlightBase\Src\General\Generic\GenericInternalMessage;
use Volantus\FlightBase\Src\General\Generic\IncomingGenericInternalMessage;
use Volantus\FlightBase\Src\General\MSP\MSPRequestMessage;
use Volantus\FlightBase\Src\General\MSP\MSPResponseMessage;
use Volantus\FlightBase\Src\Server\Messaging\MessageServerService;
use Volantus\FlightBase\Src\Server\Network\Client;
use Volantus\FlightBase\Tests\Server\General\DummyConnection;
use Volantus\FlightBase\Tests\Server\Messaging\MessageServerServiceTest;
use Volantus\MspBroker\Src\Device\DeviceCommunicationService;
use Volantus\MspBroker\Src\Networking\MessageHandler;
use Volantus\MSPProtocol\Src\Protocol\Request\MotorStatus;

/**
 * Class MessageHandlerTest
 *
 * @package Volantus\MspBroker\Tests\Networking
 */
class MessageHandlerTest extends MessageServerServiceTest
{
    /**
     * @var DeviceCommunicationService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $deviceCommunicationService;

    protected function setUp()
    {
        $this->deviceCommunicationService = $this->getMockBuilder(DeviceCommunicationService::class)->disableOriginalConstructor()->getMock();
        parent::setUp();
    }

    /**
     * @return MessageServerService
     */
    protected function createService() : MessageServerService
    {
        return new MessageHandler($this->dummyOutput, $this->messageService, $this->clientFactory, $this->deviceCommunicationService);
    }

    public function test_newMessage_authenticationMessageHandledCorrectly_tokenCorrect()
    {
        $mspRequestMessage = new MSPRequestMessage(0, new MotorStatus());
        $mspResponseMessage = $this->getMockBuilder(MSPResponseMessage::class)->disableOriginalConstructor()->getMock();
        $genericMessage = new GenericInternalMessage($mspResponseMessage);

        /** @var DummyConnection|\PHPUnit_Framework_MockObject_MockObject $connection */
        $connection = $this->getMockBuilder(DummyConnection::class)->disableOriginalConstructor()->getMock();
        $connection->expects(self::once())
            ->method('send')
            ->with(self::equalTo(json_encode($genericMessage->toRawMessage())));

        $client = new Client(1, $connection, -1);
        $client->setAuthenticated();
        $this->clientFactory->method('get')->willReturn($client);

        $this->messageService->expects(self::once())
            ->method('handle')
            ->with($client, 'correct')->willReturn(new IncomingGenericInternalMessage($client, $mspRequestMessage));

        $this->deviceCommunicationService->expects(self::once())
            ->method('handleRequest')
            ->willReturn($mspResponseMessage);

        $this->messageServerService->newClient($connection);
        $this->messageServerService->newMessage($connection, 'correct');
    }
}