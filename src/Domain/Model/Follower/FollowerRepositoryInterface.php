<?php

namespace Tailgate\Domain\Model\Follower;

use Buttercup\Protects\AggregateRepository;

interface FollowerRepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}