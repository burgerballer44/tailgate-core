<?php

namespace Tailgate\Common\PasswordHashing;

interface PasswordHashingInterface
{
    public function hash($plainPassword);
    public function verify($plainPassword, $hash);
}