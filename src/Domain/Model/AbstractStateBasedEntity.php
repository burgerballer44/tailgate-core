<?php

namespace Tailgate\Domain\Model;

use Burger\Aggregate\CurrentState;
use Burger\Aggregate\IsStateBased;
use Burger\Aggregate\RecordsDomainEvents;
use Burger\Aggregate\RecordsEvents;

abstract class AbstractStateBasedEntity implements RecordsEvents, IsStateBased
{
    use RecordsDomainEvents;
    use AppliesAndRecordsDomainEvents;

    abstract public static function createEntityFromView(CurrentState $state): RecordsEvents;

    // these entities are state based
    // we need to be able to create an entity based on its stored state
    public static function reconstituteFromState(CurrentState $state): RecordsEvents
    {
        return static::createEntityFromView($state);
    }
}
