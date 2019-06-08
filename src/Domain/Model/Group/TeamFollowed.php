<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Team\TeamId;

class TeamFollowed implements DomainEvent
{
    private $followId;
    private $groupId;
    private $teamId;
    private $occurredOn;

    public function __construct(
        FollowId $followId,
        GroupId $groupId,
        TeamId $teamId
    ) {
        $this->followId = $followId;
        $this->groupId = $groupId;
        $this->teamId = $teamId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}