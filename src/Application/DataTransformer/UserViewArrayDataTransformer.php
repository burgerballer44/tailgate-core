<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\UserDataTransformerInterface;
use Tailgate\Domain\Model\User\UserView;

class UserViewArrayDataTransformer
{
    public function read(UserView $userView)
    {
        return [
            'id'       => $userView->getId(),
            'username' => $userView->getUsername(),
            'email'    => $userView->getEmail(),
            'status'   => $userView->getStatus(),
            'role'     => $userView->getRole(),
        ];
    }
}