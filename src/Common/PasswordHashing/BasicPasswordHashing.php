<?php

namespace Tailgate\Common\PasswordHashing;

class BasicPasswordHashing implements PasswordHashingInterface
{
    public function hash($plainPassword) : string
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    public function verify($plainPassword, $hash) : bool
    {
        return password_verify($plainPassword, $hash);
    }
}
