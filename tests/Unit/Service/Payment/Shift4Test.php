<?php

namespace App\Tests\Unit\Service\Payment;

use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\DTO\Output\PaymentResponse;
use App\Application\Service\Payment\Shift4Provider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shift4\Response\Charge;
use Shift4\Shift4Gateway;

class Shift4Test extends TestCase
{
    private $shift4Provider;
    private $loggerMock;
    private $shift4GatewayMock;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->shift4GatewayMock = $this->createMock(Shift4Gateway::class);

        $this->shift4Provider = $this->getMockBuilder(Shift4Provider::class)
            ->setConstructorArgs(['test-api-key', $this->loggerMock])
            ->onlyMethods(['createCharge'])
            ->getMock();

        $this->shift4Provider->method('createCharge')
            ->willReturn($this->shift4GatewayMock);
    }

    public function testProcessPaymentSuccess(): void
    {
        $paymentDTO = new ProcessPaymentDTO(
            100.00,
            'USD',
            '4200000000000000',
            12,
            2026,
            '123'
        );

        $chargeMock = $this->getMockBuilder('stdClass')
            ->addMethods(['getId', 'getCreated', 'getAmount', 'getCurrency', 'getCard'])
            ->getMock();

        $this->shift4GatewayMock->expects($this->once())
            ->method('createCharge')
            ->willReturn($chargeMock);

        $result = $this->shift4Provider->processPayment($paymentDTO);


        $this->assertInstanceOf(PaymentResponse::class, $result);
    }
}