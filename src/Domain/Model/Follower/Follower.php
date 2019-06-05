<?php

namespace Tailgate\Domain\Model\Follower;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IsEventSourced;
use Buttercup\Protects\RecordsEvents;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Group\GroupId;
use Verraes\ClassFunctions\ClassFunctions;

class Follower implements RecordsEvents, IsEventSourced
{
    private $followerId;
    private $groupId;
    private $teamId;
    private $recordedEvents = [];

    private function __construct($followerId, $groupId, $teamId)
    {
        $this->followerId = $followerId;
        $this->groupId = $groupId;
        $this->teamId = $teamId;
    }

    public static function create(FollowerId $followerId, GroupId $groupId, TeamId $teamId)
    {
        $newFollower = new Follower($followerId, $groupId, $teamId);

        $newFollower->recordThat(
            new TeamFollowed($followerId, $groupId, $teamId)
        );

        return $newFollower;
    }

    public function getId()
    {
        return (string) $this->followerId;
    }

    public function getGroupId()
    {
        return (string) $this->groupId;
    }

    public function getTeamId()
    {
        return (string) $this->teamId;
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
        $group = new Follower($aggregateHistory->getAggregateId(), '', '');

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

    private function recordThat(DomainEvent $domainEvent)
    {
        $this->recordedEvents[] = $domainEvent;
    }

    private function applyAndRecordThat(DomainEvent $aDomainEvent)
    {
        $this->recordThat($aDomainEvent);
        $this->apply($aDomainEvent);
    }

    private function applyTeamFollowed(TeamFollowed $event)
    {
        $this->groupId = $event->getGroupId();
        $this->teamId = $event->getTeamId();
    }
}
