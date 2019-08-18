<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Group\GroupId;

class TeamFollowed implements DomainEvent, TeamDomainEvent
{
    private $followId;
    private $teamId;
    private $groupId;
    private $occurredOn;

    public function __construct(
        FollowId $followId,
        TeamId $teamId,
        GroupId $groupId
    ) {
        $this->followId = $followId;
        $this->teamId = $teamId;
        $this->groupId = $groupId;
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

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
