<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends Exception
{
    protected $code = Response::HTTP_UNAUTHORIZED;
    protected $message = 'Unauthorized. Authentication required.';

    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? $this->message, $this->code);
    }
}

