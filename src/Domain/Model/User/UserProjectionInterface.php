<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Common\Projection\ProjectionInterface;

interface UserProjectionInterface extends ProjectionInterface
{
    public function projectUserRegistered(UserRegistered $event);
    public function projectUserActivated(UserActivated $event);
    public function projectUserDeleted(UserDeleted $event);
    public function projectPasswordUpdated(PasswordUpdated $event);
    public function projectEmailUpdated(EmailUpdated $event);
    public function projectUserUpdated(UserUpdated $event);
}
