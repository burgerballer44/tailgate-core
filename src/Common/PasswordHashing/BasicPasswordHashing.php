<?php

namespace Tailgate\Common\PasswordHashing;

class BasicPasswordHashing implements PasswordHashingInterface
{
    public function hash($plainPassword)
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    public function verify($plainPassword, $hash)
    {
        return password_verify($plainPassword, $hash);
    }
}
