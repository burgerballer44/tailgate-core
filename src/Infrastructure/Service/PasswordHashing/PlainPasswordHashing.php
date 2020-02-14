<?php

namespace Tailgate\Infrastructure\Service\PasswordHashing;

use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;

class PlainPasswordHashing implements PasswordHashingInterface
{
    public function hash($plainPassword) : string
    {
        return $plainPassword;
    }

    public function verify($plainPassword, $hash) : bool
    {
        return $plainPassword === $hash;
    }
}
