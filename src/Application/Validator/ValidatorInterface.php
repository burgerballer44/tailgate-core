<?php

namespace Tailgate\Application\Validator;

// ensure a command or query is valid
interface ValidatorInterface
{
    public function assert($command): bool;

    public function errors(): array;
}
