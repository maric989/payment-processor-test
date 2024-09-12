<?php

namespace App\Application\Contract;

interface BaseRequestInterface
{
    public function populate(): void;

    public function validate(): void;
}