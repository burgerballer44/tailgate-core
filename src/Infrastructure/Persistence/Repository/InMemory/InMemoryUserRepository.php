<?php

namespace Tailgate\Infrastructure\Persistence\Repository\InMemory;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class InMemoryUserRepository implements UserRepositoryInterface
{

    private $users = [];

    public function byId(UserId $userId)
    {
        if (!isset($this->users[(string) $userId])) {
            return;
        }

        return $this->users[(string) $userId];
    }

    public function add(User $user)
    {
        $this->users[$user->getId()] = $user;
    }

    public function nextIdentity()
    {
        return new UserId();
    }
}