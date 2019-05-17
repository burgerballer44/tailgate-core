<?php

namespace Tailgate\Application\DataTransformer\User;

use Tailgate\Domain\Model\User\User;

interface UserDataTransformer
{
    public function write(User $user);
    public function read();
}