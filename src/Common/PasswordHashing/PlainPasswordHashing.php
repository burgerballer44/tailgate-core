<?php

namespace Tailgate\Common\PasswordHashing;

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
