<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\DomainEvent;
use Tailgate\Domain\Model\Season\GameId;

class ScoreSubmitted implements DomainEvent, GroupDomainEvent
{
    private $groupId;
    private $scoreId;
    private $playerId;
    private $gameId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;
    private $dateOccurred;

    public function __construct(
        GroupId $groupId,
        ScoreId $scoreId,
        PlayerId $playerId,
        GameId $gameId,
        $homeTeamPrediction,
        $awayTeamPrediction,
        Date $dateOccurred
    ) {
        $this->groupId = $groupId;
        $this->scoreId = $scoreId;
        $this->playerId = $playerId;
        $this->gameId = $gameId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'A player has submitted a score.';
    }

    public function getAggregateId()
    {
        return $this->groupId;
    }

    public function getScoreId()
    {
        return $this->scoreId;
    }

    public function getPlayerId()
    {
        return $this->playerId;
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
