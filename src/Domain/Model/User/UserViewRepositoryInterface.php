<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\User\UserId;

interface UserViewRepositoryInterface
{
    public function get(UserId $id);
    public function all();
    public function byEmail($email);
    public function byPasswordResetToken($passwordResetToken);
}
