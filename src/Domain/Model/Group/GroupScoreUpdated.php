<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Season\GameId;

class GroupScoreUpdated implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $scoreId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;
    private $occurredOn;

    public function __construct(
        GroupId $groupId,
        ScoreId $scoreId,
        $homeTeamPrediction,
        $awayTeamPrediction
    ) {
        $this->groupId = $groupId;
        $this->scoreId = $scoreId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getScoreId()
    {
        return $this->scoreId;
    }

    public function getHomeTeamPrediction()
    {
        return $this->homeTeamPrediction;
    }

    public function getAwayTeamPrediction()
    {
        return $this->awayTeamPrediction;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
