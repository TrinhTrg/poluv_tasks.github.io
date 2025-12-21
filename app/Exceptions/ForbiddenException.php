<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ForbiddenException extends Exception
{
    protected $code = Response::HTTP_FORBIDDEN;
    protected $message = 'Forbidden. You do not have permission to access this resource.';

    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? $this->message, $this->code);
    }
}

