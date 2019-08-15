<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\AggregateRepository;

interface UserRepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}
