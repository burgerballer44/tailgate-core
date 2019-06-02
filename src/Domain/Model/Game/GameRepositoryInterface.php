<?php

namespace Tailgate\Domain\Model\Game;

use Buttercup\Protects\AggregateRepository;

interface GameRepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}