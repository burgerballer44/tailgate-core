<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;
use Tailgate\Domain\Model\User\UserView;

class UserViewArrayDataTransformer implements UserDataTransformerInterface
{
    public function read(UserView $userView)
    {
        return [
            'userId'   => $userView->getUserId(),
            'email'    => $userView->getEmail(),
            'status'   => $userView->getStatus(),
            'role'     => $userView->getRole(),
        ];
    }
}
