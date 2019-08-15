<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\AggregateRepository;

interface TeamRepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}
