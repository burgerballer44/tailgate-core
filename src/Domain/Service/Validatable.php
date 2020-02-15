<?php

namespace Tailgate\Domain\Service;

use Tailgate\Application\Validator\ValidationException;

trait Validatable
{
    public function validate($command)
    {
        if (!$this->validator->assert($command)) {
            throw new ValidationException($this->validator->errors());
        }
    }
}
