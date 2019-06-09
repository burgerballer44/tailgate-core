<?php

namespace Tailgate\Domain\Model;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\IsEventSourced;
use Buttercup\Protects\RecordsEvents;
use Verraes\ClassFunctions\ClassFunctions;

abstract class AbstractEntity implements RecordsEvents, IsEventSourced
{
    private $recordedEvents = [];

    abstract static protected function createEmptyEntity(IdentifiesAggregate $id);

    public function getRecordedEvents()
    {
        return new DomainEvents($this->recordedEvents);
    }

    public function clearRecordedEvents()
    {
        $this->recordedEvents = [];
    }

    public static function reconstituteFrom(AggregateHistory $aggregateHistory)
    {
        $entity = static::createEmptyEntity($aggregateHistory->getAggregateId());

        foreach ($aggregateHistory as $event) {
            $entity->apply($event);
        }

        return $entity;
    }

    private function apply($anEvent)
    {
        $method = 'apply' . ClassFunctions::short($anEvent);
        $this->$method($anEvent);
    }

    protected function recordThat(DomainEvent $domainEvent)
    {
        $this->recordedEvents[] = $domainEvent;
    }

    protected function applyAndRecordThat(DomainEvent $aDomainEvent)
    {
        $this->recordThat($aDomainEvent);
        $this->apply($aDomainEvent);
    }
}