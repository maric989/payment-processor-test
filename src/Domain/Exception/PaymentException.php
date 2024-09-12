<?php

namespace App\Domain\Exception;

use Exception;

class PaymentException extends Exception
{
    protected $message = 'Payment failed';
}