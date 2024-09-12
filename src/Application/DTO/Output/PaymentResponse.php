<?php

namespace App\Application\DTO\Output;

class PaymentResponse
{
    public function __construct(
        public string $transactionId,
        public string $date,
        public float $amount,
        public string $currency,
        public string $cardBin
    ) {
        $this->amount = number_format($amount / 100, 2, '.', '');
    }
}