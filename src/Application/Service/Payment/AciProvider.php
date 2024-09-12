<?php

namespace App\Application\Service\Payment;

use App\Application\Contract\PaymentProcessorInterface;
use App\Application\DTO\Input\Payment\Aci\ChargeRequestDTO;
use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\DTO\Output\PaymentResponse;
use App\Application\Service\Http\CurlClient;
use App\Domain\Exception\PaymentException;
use Psr\Log\LoggerInterface;

class AciProvider implements PaymentProcessorInterface
{
    public const ACI_URL = "https://eu-test.oppwa.com/v1/payments";

    public function __construct(
        private readonly CurlClient $curlClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function processPayment(ProcessPaymentDTO $paymentDTO): PaymentResponse
    {
        try {
            $chargeDto = new ChargeRequestDTO(
                amount: $paymentDTO->amount,
                currency: $paymentDTO->currency,
                cardNumber: $paymentDTO->cardNumber,
                expiryMonth: $paymentDTO->cardExpMonth,
                expiryYear: $paymentDTO->cardExpYear,
                cvv: $paymentDTO->cardCvv,
            );

            $responseData = $this->curlClient->postRequest(self::ACI_URL, $chargeDto->preparePaymentData());
            $responseData = json_decode($responseData, true);

            return new PaymentResponse(
                transactionId: $responseData['id'],
                date: $responseData['timestamp'],
                amount: $responseData['amount'],
                currency: $responseData['currency'],
                cardBin: $responseData['card']['bin']
            );
        } catch (\Exception $exception) {
            $this->logger->error('ACI payment processing failed.', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTraceAsString(),
                'paymentData' => [
                    'amount' => $paymentDTO->amount,
                    'currency' => $paymentDTO->currency,
                    'cardNumber' => substr($paymentDTO->cardNumber, 0, 6) . '******',
                    'cardExpMonth' => $paymentDTO->cardExpMonth,
                    'cardExpYear' => $paymentDTO->cardExpYear,
                ]
            ]);

            throw new PaymentException('Payment processing failed: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}