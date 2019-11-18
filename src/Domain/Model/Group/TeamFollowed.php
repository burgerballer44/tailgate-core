<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonId;

class TeamFollowed implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $followId;
    private $teamId;
    private $seasonId;
    private $occurredOn;

    public function __construct(
        GroupId $groupId,
        FollowId $followId,
        TeamId $teamId,
        SeasonId $seasonId
    ) {
        $this->groupId = $groupId;
        $this->followId = $followId;
        $this->teamId = $teamId;
        $this->seasonId = $seasonId;
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

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
