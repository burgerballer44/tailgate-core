<?php

namespace Tailgate\Domain\Model\Common;

use InvalidArgumentException;

class Email
{
    private $value;

    private function __construct(string $value)
    {
        if ($value != "" && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("invalid email entered: {$value}");
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
