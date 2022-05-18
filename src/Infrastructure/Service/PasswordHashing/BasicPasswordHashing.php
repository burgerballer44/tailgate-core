<?php

namespace Tailgate\Infrastructure\Service\PasswordHashing;

use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;

class BasicPasswordHashing implements PasswordHashingInterface
{
    public function hash($plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    public function verify($plainPassword, $hash): bool
    {
        return password_verify($plainPassword, $hash);
    }
}
