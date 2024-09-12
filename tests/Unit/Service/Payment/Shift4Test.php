<?php

namespace App\Tests\Unit\Service\Payment;

use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\DTO\Output\PaymentResponse;
use App\Application\Service\Payment\Shift4Provider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class Shift4Test extends TestCase
{
    private $shift4Provider;
    private $loggerMock;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->shift4Provider = $this->getMockBuilder(Shift4Provider::class)
            ->setConstructorArgs(['test-api-key', $this->loggerMock])
            ->onlyMethods(['processPayment'])
            ->getMock();
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

        $result = $this->shift4Provider->processPayment($paymentDTO);

        $this->assertInstanceOf(PaymentResponse::class, $result);
    }
}