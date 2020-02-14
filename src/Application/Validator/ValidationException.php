<?php

namespace Tailgate\Application\Validator;

use Exception;
use Throwable;

// exception for failing validation
class ValidationException extends Exception
{
    private $validationErrors;

    public function __construct(array $validationErrors, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->validationErrors = $validationErrors;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
