<?php

namespace App\Domain\Exception;

use Exception;

class InvalidPaymentProviderException extends Exception
{
    protected $message = 'Invalid Payment Provider';
}