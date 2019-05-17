<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\User;

interface UserRepositoryInterface
{
    public function nextIdentity();
    public function byId(UserId $userId);
    public function add(User $user);
}