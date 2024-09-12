<?php

namespace App\Application\Factory;

use App\Application\Contract\PaymentProcessorInterface;
use App\Application\Service\Payment\AciProvider;
use App\Application\Service\Payment\Shift4Provider;
use App\Domain\Exception\InvalidPaymentProviderException;

class PaymentProviderFactory
{
    public function __construct(
        private readonly AciProvider    $aciProvider,
        private readonly Shift4Provider $shift4Provider,
    ) {
    }

    public function getProvider(string $provider): PaymentProcessorInterface
    {
        return match ($provider) {
            'shift4' => $this->shift4Provider,
            'aci' => $this->aciProvider,
            default => throw new InvalidPaymentProviderException(),
        };
    }
}