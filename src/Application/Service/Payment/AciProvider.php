<?php

namespace App\Application\Service\Payment;

use App\Application\Contract\PaymentProcessorInterface;
use App\Application\DTO\Input\Payment\Aci\ChargeRequestDTO;
use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\DTO\Output\PaymentResponse;
use App\Domain\Exception\PaymentException;
use Psr\Log\LoggerInterface;

class AciProvider implements PaymentProcessorInterface
{
    private const ACI_URL = "https://eu-test.oppwa.com/v1/payments";

    public function __construct(
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

            $responseData = $this->makeCurlRequest($chargeDto->preparePaymentData());
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

    private function makeCurlRequest(string $data): string|false
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::ACI_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg='));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseData = curl_exec($ch);

        if(curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);

            $this->logger->error('cURL error during ACI payment request.', [
                'error' => $errorMessage,
                'paymentData' => $data
            ]);

            throw new PaymentException('Payment processing failed: ' . $errorMessage);
        }

        curl_close($ch);
        return $responseData;
    }
}