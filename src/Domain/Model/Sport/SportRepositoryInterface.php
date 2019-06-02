<?php

namespace Tailgate\Domain\Model\Sport;

use Buttercup\Protects\AggregateRepository;

interface SportRepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}