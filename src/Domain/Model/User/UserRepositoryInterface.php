<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\AggregateRepository;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;

interface UserRepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}