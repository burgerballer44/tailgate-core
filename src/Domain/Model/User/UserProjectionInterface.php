<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Common\Projection\ProjectionInterface;

interface UserProjectionInterface extends ProjectionInterface
{
    public function projectUserSignedUp(UserSignedUp $event);
}