<?php

namespace Tailgate\Application\DataTransformer\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Application\DataTransformer\User\UserDataTransformer;

class UserDtoDataTransformer implements UserDataTransformer
{
    private $user;

    public function write(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function read()
    {
        return [
            'id' => $this->user->getId(),
            'username' =>  $this->user->getUsername(),
            'email' =>  $this->user->getEmail(),
        ];
    }
}