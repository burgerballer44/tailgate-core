<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\AggregateRepository;

interface GroupRepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}
