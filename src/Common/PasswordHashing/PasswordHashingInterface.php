<?php

namespace Tailgate\Common\PasswordHashing;

interface PasswordHashingInterface
{
    public function hash($plainPassword) : string;
    public function verify($plainPassword, $hash) : bool;
}
