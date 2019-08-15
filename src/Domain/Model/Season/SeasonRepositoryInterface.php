<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\AggregateRepository;

interface SeasonRepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}
