<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\UserDataTransformerInterface;
use Tailgate\Domain\Model\User\UserView;

class UserViewArrayDataTransformer implements UserDataTransformerInterface
{
    public function read(UserView $userView)
    {
        return [
            'userId'   => $userView->getUserId(),
            'username' => $userView->getUsername(),
            'email'    => $userView->getEmail(),
            'status'   => $userView->getStatus(),
            'role'     => $userView->getRole(),
        ];
    }
}
