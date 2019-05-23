<?php

namespace Tailgate\Infrastructure\Persistence\Projection\InMemory;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserSignedUp;
use Tailgate\Domain\Model\User\UserProjectionInterface;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class InMemoryUserProjectionViewRepository extends AbstractProjection implements UserProjectionInterface, UserViewRepositoryInterface
{
    private $users = [];

    public function get(UserId $userId)
    {
        if (!isset($this->users[(string) $userId])) {
            return;
        }
        return $this->users[(string) $userId];
    }

    public function all()
    {
        return $this->users;
    }

    public function projectUserSignedUp(UserSignedUp $event)
    {
        $this->users[(string) $event->getAggregateId()] = $event;
    }
}