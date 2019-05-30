<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IsEventSourced;
use Buttercup\Protects\RecordsEvents;
use Tailgate\Domain\Model\User\UserId;
use Verraes\ClassFunctions\ClassFunctions;

class Group implements RecordsEvents, IsEventSourced
{
    private $groupId;
    private $name;
    private $ownerId;
    private $recordedEvents = [];

    private function __construct($groupId, $name, $ownerId)
    {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->ownerId = $ownerId;
    }

    public static function create(GroupId $groupId, $name, UserId $ownerId)
    {
        $newGroup = new Group($groupId, $name, $ownerId);

        $newGroup->recordThat(
            new GroupCreated($groupId, $name, $ownerId)
        );

        return $newGroup;
    }

    public function getId()
    {
        return (string) $this->groupId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOwnerId()
    {
        return (string) $this->ownerId;
    }

    private function recordThat(DomainEvent $domainEvent)
    {
        $this->recordedEvents[] = $domainEvent;
    }

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
        $group = new Group($aggregateHistory->getAggregateId(), '', '');

        foreach ($aggregateHistory as $event) {
            $group->apply($event);
        }

        return $group;
    }

    private function apply($anEvent)
    {
        $method = 'apply' . ClassFunctions::short($anEvent);
        $this->$method($anEvent);
    }

    private function applyGroupCreated(GroupCreated $event)
    {
        $this->name = $event->getName();
        $this->ownerId = $event->getOwnerId();
    }
}
