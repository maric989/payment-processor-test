<?php

namespace App\Application\DTO\Input;

class ProcessPaymentDTO
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $cardNumber,
        public int $cardExpYear,
        public int $cardExpMonth,
        public string $cardCvv,
    ) {
    }
}