<?php

namespace Tailgate\Application\Command\Group;

class SubmitScoreForGroupCommand
{
    private $groupId;
    private $playerId;
    private $gameId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;

    public function __construct(
        string $groupId,
        string $playerId,
        string $gameId,
        int $homeTeamPrediction,
        int $awayTeamPrediction
    ) {
        $this->groupId = $groupId;
        $this->playerId = $playerId;
        $this->gameId = $gameId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
    }

    public function getGroupId()
    {
        return $this->groupId;
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
}
