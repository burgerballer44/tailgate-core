<?php

namespace Tailgate\Domain\Service;

use Tailgate\Application\Validator\ValidationException;
use Tailgate\Application\Validator\ValidatorInterface;

abstract class AbstractService
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($command)
    {
        if (!$this->validator->assert($command)) {
            throw new ValidationException($this->validator->errors());
        }
    }
}
