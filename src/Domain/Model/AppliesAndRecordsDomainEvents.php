<?php

namespace Tailgate\Domain\Model;

use Tailgate\Domain\Model\DomainEvent;
use Burger\Common\ClassFunctions;

trait AppliesAndRecordsDomainEvents
{
    // apply the event to the entity
    private function apply(DomainEvent $domainEvent)
    {
        $method = 'apply' . ClassFunctions::short($domainEvent);
        $this->$method($domainEvent);
    }

    // record the domain event happened to the entity
    protected function recordThat(DomainEvent $domainEvent)
    {
        $this->recordedEvents[] = $domainEvent;
    }

    // apply and record the domain event to the entity
    protected function applyAndRecordThat(DomainEvent $domainEvent)
    {
        $this->recordThat($domainEvent);
        $this->apply($domainEvent);
    }
}