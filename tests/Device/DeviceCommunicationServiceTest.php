<?php
namespace Volantus\MspBroker\Tests\Device;

use Volantus\FlightBase\Src\General\MSP\MSPRequestFailedMessage;
use Volantus\FlightBase\Src\General\MSP\MSPRequestMessage;
use Volantus\FlightBase\Src\General\MSP\MSPResponseMessage;
use Volantus\MspBroker\Src\Device\DeviceCommunicationService;
use Volantus\MSPProtocol\Src\Protocol\CommunicationService;
use Volantus\MSPProtocol\Src\Protocol\Request\MotorStatus as MotorStatusRequest;
use Volantus\MSPProtocol\Src\Protocol\Response\MotorStatus as MotorStatusResponse;

/**
 * Class DeviceCommunicationServiceTest
 *
 * @package Volantus\MspBroker\Tests\Device
 */
class DeviceCommunicationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommunicationService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serialService;

    /**
     * @var DeviceCommunicationService
     */
    private $service;

    protected function setUp()
    {
        $this->serialService = $this->getMockBuilder(CommunicationService::class)->disableOriginalConstructor()->getMock();
        $this->service = new DeviceCommunicationService($this->serialService);
    }

    public function test_handleRequest_successful()
    {
        $mspRequest = new MotorStatusRequest();
        $mspResponse = $this->getMockBuilder(MotorStatusResponse::class)->disableOriginalConstructor()->getMock();
        $message = new MSPRequestMessage(0, $mspRequest);

        $this->serialService->expects(self::once())
            ->method('send')
            ->with(self::equalTo($mspRequest))
            ->willReturn($mspResponse);

        $result = $this->service->handleRequest($message);
        self::assertInstanceOf(MSPResponseMessage::class, $result);
        self::assertEquals($mspResponse, $result->getMspResponse());
        self::assertEquals($message->getId(), $result->getId());
    }

    public function test_handleRequest_failed()
    {
        $mspRequest = new MotorStatusRequest();
        $message = new MSPRequestMessage(0, $mspRequest);

        $this->serialService->expects(self::once())
            ->method('send')
            ->with(self::equalTo($mspRequest))
            ->willThrowException(new \Exception('test'));

        $result = $this->service->handleRequest($message);
        self::assertInstanceOf(MSPRequestFailedMessage::class, $result);
        self::assertEquals($message->getId(), $result->getId());
    }
}