<?php

namespace App\Application\DTO\Input\Payment\Aci;

class ChargeRequestDTO
{
    private const PAYMENT_DATA = "entityId=8a8294174b7ecb28014b9699220015ca" .
    "&amount=%s" .
    "&currency=%s" .
    "&paymentBrand=VISA" .
    "&paymentType=DB" .
    "&card.number=%s" .
    "&card.holder=%s" .
    "&card.expiryMonth=%s" .
    "&card.expiryYear=%s" .
    "&card.cvv=%s";

    public function __construct(
        public float $amount,
        public string $currency,
        public string $cardNumber,
        public int $expiryMonth,
        public int $expiryYear,
        public int $cvv,
        public string $cardHolder = 'Jane Jones'
    ) {
        $this->amount = $amount * 100;
    }

    public function preparePaymentData(): string
    {
        return sprintf(self::PAYMENT_DATA,
            $this->amount,
            $this->currency,
            $this->cardNumber,
            $this->cardHolder,
            $this->expiryMonth,
            $this->expiryYear,
            $this->cvv,
        );
    }
}