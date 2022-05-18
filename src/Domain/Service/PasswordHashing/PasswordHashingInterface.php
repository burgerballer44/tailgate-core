<?php

namespace Tailgate\Domain\Service\PasswordHashing;

interface PasswordHashingInterface
{
    public function hash($plainPassword): string;

    public function verify($plainPassword, $hash): bool;
}
