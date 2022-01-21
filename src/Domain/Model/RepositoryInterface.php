<?php

namespace Tailgate\Domain\Model;

use Burger\Aggregate\EventSourcedRepository;

interface RepositoryInterface extends EventSourcedRepository
{
    public function nextIdentity();
}
