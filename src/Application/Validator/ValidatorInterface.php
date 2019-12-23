<?php

namespace Tailgate\Application\Validator;

use Tailgate\Application\Validator\ValidationResult;

interface ValidatorInterface
{
    public function assert($command): bool;
    public function errors(): array;
}
