<?php

namespace Tailgate\Common\Projection;

use Buttercup\Protects\DomainEvents;

interface ProjectionInterface
{
    public function project(DomainEvents $eventStream);
}