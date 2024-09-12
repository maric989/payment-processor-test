<?php

namespace App\Application\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ProcessPaymentRequest extends BaseRequest
{
    #[Assert\NotBlank(message: 'Amount is required')]
    #[Assert\Positive(message: 'Amount must be a positive number')]
    public ?float $amount;

    #[Assert\NotBlank(message: 'Currency is required')]
    #[Assert\Choice(choices: ['USD', 'EUR'], message: 'Invalid currency. Accepted values: USD, EUR')]
    public ?string $currency;

    #[Assert\NotBlank(message: 'Card number is required')]
    #[Assert\Length(min: 13, max: 19, minMessage: 'Card number must be between 13 and 19 digits')]
    public ?string $card_number;

    #[Assert\NotBlank(message: 'Card expiration year is required')]
    #[Assert\Positive(message: 'Card expiration year must be a positive number')]
    public ?int $card_exp_year;

    #[Assert\NotBlank(message: 'Card expiration month is required')]
    #[Assert\Positive(message: 'Card expiration month must be a positive number')]
    #[Assert\Range(notInRangeMessage: 'Card expiration month must be between 1 and 12', min: 1, max: 12)]
    public int $card_exp_month;

    #[Assert\NotBlank(message: 'CVV is required')]
    #[Assert\Length(min: 3, max: 4, minMessage: 'CVV must be 3 or 4 digits')]
    public string $card_cvv;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCardNumber(): string
    {
        return $this->card_number;
    }

    public function getCardExpYear(): int
    {
        return $this->card_exp_year;
    }

    public function getCardExpMonth(): int
    {
        return $this->card_exp_month;
    }

    public function getCardCvv(): string
    {
        return $this->card_cvv;
    }
}