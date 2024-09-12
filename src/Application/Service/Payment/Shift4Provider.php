<?php

namespace App\Application\Service\Payment;

use App\Application\Contract\PaymentProcessorInterface;
use App\Application\DTO\Input\Payment\Shift4\ChargeRequestDTO;
use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\DTO\Output\PaymentResponse;
use App\Domain\Exception\PaymentException;
use Psr\Log\LoggerInterface;
use Shift4\Exception\Shift4Exception;
use Shift4\Shift4Gateway;

class Shift4Provider implements PaymentProcessorInterface
{
    public function __construct(
        private readonly string $shift4ApiKey,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function processPayment(ProcessPaymentDTO $paymentDTO): PaymentResponse
    {
        $gateway = new Shift4Gateway($this->shift4ApiKey);
        $chargeDTO = new ChargeRequestDTO(
            amount: $paymentDTO->amount,
            currency: $paymentDTO->currency,
            cardNumber: $paymentDTO->cardNumber,
            cardExpMonth: $paymentDTO->cardExpMonth,
            cardExpYear: $paymentDTO->cardExpYear,
            cardCvc: $paymentDTO->cardCvv,
        );

        try {
            $charge = $gateway->createCharge($chargeDTO->preparePaymentData());

            return new PaymentResponse(
                transactionId: $charge->getId(),
                date: date('Y-m-d', $charge->getCreated()),
                amount: $charge->getAmount(),
                currency: $charge->getCurrency(),
                cardBin: $charge->getCard()->getFirst6()
            );
        } catch (Shift4Exception $shift4Exception) {
            $this->logger->error('Shift4 payment error: ' . $shift4Exception->getMessage(), [
                'errorType' => $shift4Exception->getType(),
                'errorCode' => $shift4Exception->getCode(),
            ]);

            throw new PaymentException('Payment processing failed: ' . $shift4Exception->getMessage());
        } catch (\Exception $e) {
            $this->logger->error('Payment processing error: ' . $e->getMessage());

            throw new \RuntimeException('An unexpected error occurred during payment processing.');
        }
    }
}