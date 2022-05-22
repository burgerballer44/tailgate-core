<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\DomainEvent;

class GroupScoreUpdated implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $scoreId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;
    private $dateOccurred;

    public function __construct(
        GroupId $groupId,
        ScoreId $scoreId,
        $homeTeamPrediction,
        $awayTeamPrediction,
        Date $dateOccurred
    ) {
        $this->groupId = $groupId;
        $this->scoreId = $scoreId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'A score in the group has been updated.';
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
