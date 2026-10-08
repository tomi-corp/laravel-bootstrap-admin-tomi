<?php

namespace App\Exceptions;

use Exception;

class PrivyDigitalIdException extends Exception
{
    public function __construct(
        string $message,
        public string $errorCode = 'privy_error',
        public int $httpStatus = 502,
        public ?int $privyStatus = null,
        public ?string $privyCode = null,
        public mixed $privyBody = null,
    ) {
        parent::__construct($message);
    }
}
