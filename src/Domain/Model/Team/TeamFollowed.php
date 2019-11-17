<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Season\SeasonId;

class TeamFollowed implements DomainEvent, TeamDomainEvent
{
    private $teamId;
    private $followId;
    private $groupId;
    private $seasonId;
    private $occurredOn;

    public function __construct(
        TeamId $teamId,
        FollowId $followId,
        GroupId $groupId,
        SeasonId $seasonId
    ) {
        $this->teamId = $teamId;
        $this->followId = $followId;
        $this->groupId = $groupId;
        $this->seasonId = $seasonId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->teamId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
