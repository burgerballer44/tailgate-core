<?php

namespace Tailgate\Domain\Model\Follower;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;

class TeamFollowed implements DomainEvent
{
    private $followerId;
    private $groupId;
    private $teamId;
    private $occurredOn;

    public function __construct(
        FollowerId $followerId,
        GroupId $groupId,
        TeamId $teamId
    ) {
        $this->followerId = $followerId;
        $this->groupId = $groupId;
        $this->teamId = $teamId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->followerId;
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