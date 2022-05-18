<?php

namespace Tailgate\Domain\Model;

use Burger\Aggregate\DomainEvent as CoreDomainEvent;

interface DomainEvent extends CoreDomainEvent
{
    // a human readable message explaining the event
    public function getEventDescription(): string;

    // return the time the event occured
    public function getDateOccurred();
}
