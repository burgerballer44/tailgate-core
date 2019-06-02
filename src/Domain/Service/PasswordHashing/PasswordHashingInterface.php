<?php

namespace Tailgate\Domain\Service\PasswordHashing;

interface PasswordHashingInterface
{
    public function hash($plainPassword);
    public function verify($plainPassword, $hash);
}