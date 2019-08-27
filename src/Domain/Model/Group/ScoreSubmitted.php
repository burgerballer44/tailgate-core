<?php

namespace Tailgate\Domain\Model\Group;

use Buttercup\Protects\DomainEvent;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Season\GameId;

class ScoreSubmitted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $scoreId;
    private $userId;
    private $gameId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;
    private $occurredOn;

    public function __construct(
        GroupId $groupId,
        ScoreId $scoreId,
        UserId $userId,
        GameId $gameId,
        $homeTeamPrediction,
        $awayTeamPrediction
    ) {
        $this->groupId = $groupId;
        $this->scoreId = $scoreId;
        $this->userId = $userId;
        $this->gameId = $gameId;
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

    public function getUserId()
    {
        return $this->userId;
    }

    public function getGameId()
    {
        return $this->gameId;
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
