<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\User\UserView;
use Tailgate\Domain\Service\DataTransformer\UserDataTransformerInterface;

class UserViewArrayDataTransformer implements UserDataTransformerInterface
{
    public function read(UserView $userView)
    {
        return [
            'user_id' => $userView->getUserId(),
            'email' => $userView->getEmail(),
            'status' => $userView->getStatus(),
            'role' => $userView->getRole(),
        ];
    }
}
