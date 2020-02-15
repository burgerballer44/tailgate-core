<?php

namespace Tailgate\Infrastructure\Persistence\Projection;

use Tailgate\Domain\Model\User\EmailUpdated;
use Tailgate\Domain\Model\User\PasswordResetTokenCreated;
use Tailgate\Domain\Model\User\PasswordUpdated;
use Tailgate\Domain\Model\User\UserActivated;
use Tailgate\Domain\Model\User\UserDeleted;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Domain\Model\User\UserUpdated;
use Tailgate\Infrastructure\Persistence\Projection\ProjectionInterface;

interface UserProjectionInterface extends ProjectionInterface
{
    public function projectUserRegistered(UserRegistered $event);
    public function projectUserActivated(UserActivated $event);
    public function projectUserDeleted(UserDeleted $event);
    public function projectPasswordUpdated(PasswordUpdated $event);
    public function projectEmailUpdated(EmailUpdated $event);
    public function projectUserUpdated(UserUpdated $event);
    public function projectPasswordResetTokenCreated(PasswordResetTokenCreated $event);
}
