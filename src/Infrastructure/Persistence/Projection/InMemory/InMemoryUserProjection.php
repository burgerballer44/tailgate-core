<?php

namespace Tailgate\Infrastructure\Persistence\Projection\InMemory;

use Tailgate\Domain\Model\User\UserSignedUp;
use Tailgate\Domain\Model\User\UserProjectionInterface;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class InMemoryUserProjection extends AbstractProjection implements UserProjectionInterface
{
    private $users = [];

    public function projectUserSignedUp(UserSignedUp $event)
    {
        
    }
}