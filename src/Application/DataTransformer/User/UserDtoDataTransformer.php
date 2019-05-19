<?php

namespace Tailgate\Application\DataTransformer\User;

use Tailgate\Application\DataTransformer\User\UserDataTransformerInterface;
use Tailgate\Domain\Model\User\User;

class UserDtoDataTransformer implements UserDataTransformerInterface
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
            'id'       => $this->user->getId(),
            'username' => $this->user->getUsername(),
            'email'    => $this->user->getEmail(),
        ];
    }
}