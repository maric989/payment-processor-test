<?php

namespace App\Application\DTO\Input;

class ProcessPaymentDTO
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $cardNumber,
        public string $cardExpYear,
        public string $cardExpMonth,
        public string $cardCvv,
    ) {
    }
}