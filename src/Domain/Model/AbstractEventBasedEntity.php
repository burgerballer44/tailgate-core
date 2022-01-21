<?php

namespace Tailgate\Domain\Model;

use Burger\Aggregate\AggregateHistory;
use Burger\Aggregate\IdentifiesAggregate;
use Burger\Aggregate\IsEventSourced;
use Burger\Aggregate\RecordsDomainEvents;
use Burger\Aggregate\RecordsEvents;
use Tailgate\Domain\Model\AppliesAndRecordsDomainEvents;

abstract class AbstractEventBasedEntity implements RecordsEvents, IsEventSourced
{
    use RecordsDomainEvents, AppliesAndRecordsDomainEvents;

   abstract protected static function createEmptyEntity(IdentifiesAggregate $id);

   // these entities are event based
   // we need to be able to create an entity based on its event history
   public static function reconstituteFromEvents(AggregateHistory $aggregateHistory) : RecordsEvents
   {
       $entity = static::createEmptyEntity($aggregateHistory->getAggregateId());

       foreach ($aggregateHistory as $event) {
           $entity->apply($event);
       }

       return $entity;
   }
}
