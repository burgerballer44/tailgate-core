<?php

namespace Tailgate\Domain\Model;

use Buttercup\Protects\AggregateRepository;

interface RepositoryInterface extends AggregateRepository
{
    public function nextIdentity();
}