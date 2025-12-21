<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends Exception
{
    protected $code = Response::HTTP_NOT_FOUND;
    protected $message = 'Resource not found.';

    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? $this->message, $this->code);
    }
}

