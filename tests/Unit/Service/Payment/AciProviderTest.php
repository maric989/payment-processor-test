<?php

namespace App\Tests\Unit\Service\Payment;

use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\DTO\Output\PaymentResponse;
use App\Application\Service\Http\CurlClient;
use App\Application\Service\Payment\AciProvider;
use App\Domain\Exception\PaymentException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AciProviderTest extends TestCase
{
    private $aciProvider;
    private $curlClientMock;
    private $loggerMock;

    protected function setUp(): void
    {
        $this->curlClientMock = $this->createMock(CurlClient::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->aciProvider = new AciProvider($this->curlClientMock, $this->loggerMock);
    }

    public function testProcessPaymentSuccess(): void
    {
        $paymentDTO = new ProcessPaymentDTO(
            100.00,
            'EUR',
            '4200000000000000',
            12,
            2026,
            '123'
        );

        $mockResponse = json_encode([
            'transaction_id' => 'txn_12345',
            'timestamp' => '2024-09-12T12:00:00Z',
            'amount' => 10000,
            'currency' => 'EUR',
            'card' => ['bin' => '420000'],
        ]);

        $this->curlClientMock->expects($this->once())
            ->method('postRequest')
            ->with(AciProvider::ACI_URL, $this->isType('string'))
            ->willReturn($mockResponse);

        $result = $this->aciProvider->processPayment($paymentDTO);

        $this->assertInstanceOf(PaymentResponse::class, $result);
    }

    public function testProcessPaymentThrowsException(): void
    {
        $paymentDTO = new ProcessPaymentDTO(
            amount: 100.00,
            currency: 'USD',
            cardNumber: '4200000000000000',
            cardExpYear: 12,
            cardExpMonth: 2026,
            cardCvv: '123'
        );

        $this->curlClientMock->expects($this->once())
            ->method('postRequest')
            ->with(AciProvider::ACI_URL, $this->isType('string'))
            ->willThrowException(new \Exception('cURL error'));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('ACI payment processing failed'),
                $this->arrayHasKey('message')
            );

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Payment processing failed: cURL error');

        $this->aciProvider->processPayment($paymentDTO);
    }
}
