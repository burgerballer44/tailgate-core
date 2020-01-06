<?php

namespace Tailgate\Application\Validator;

use Tailgate\Application\Validator\ValidationResult;

// ensure a command or query is valid
interface ValidatorInterface
{
    public function assert($command): bool;
    public function errors(): array;
}
