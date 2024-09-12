<?php

namespace App\Application\DTO\Input\Payment\Shift4;

class ChargeRequestDTO
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $cardNumber,
        public int $cardExpMonth,
        public int $cardExpYear,
        public int $cardCvc,
    ) {
        $this->amount = $this->amount * 100;
    }

    public function preparePaymentData(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'card' => [
                'number' => $this->cardNumber,
                'expMonth' => $this->cardExpMonth,
                'expYear' => $this->cardExpYear,
                'cvc' => $this->cardCvc
            ]
        ];
    }
}