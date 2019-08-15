<?php

namespace Tailgate\Common\PasswordHashing;

class PlainPasswordHashing implements PasswordHashingInterface
{
    public function hash($plainPassword)
    {
        return $plainPassword;
    }

    public function verify($plainPassword, $hash)
    {
        return $plainPassword === $hash;
    }
}
