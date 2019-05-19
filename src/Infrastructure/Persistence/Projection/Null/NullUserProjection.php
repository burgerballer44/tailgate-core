<?php

namespace Tailgate\Infrastructure\Persistence\Projection\Null;

use Tailgate\Domain\Model\User\UserSignedUp;
use Tailgate\Domain\Model\User\UserProjectionInterface;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class NullUserProjection extends AbstractProjection implements UserProjectionInterface
{
    public function projectUserSignedUp(UserSignedUp $event)
    {
        return null;
    }
}