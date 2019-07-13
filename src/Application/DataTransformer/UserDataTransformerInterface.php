<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\User\UserView;

interface UserDataTransformerInterface
{
    public function read(UserView $userView);
}