<?php

namespace App\Application\Contract;

use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\DTO\Output\PaymentResponse;

interface PaymentProcessorInterface
{
    public function processPayment(ProcessPaymentDTO $paymentDTO): PaymentResponse;
}