<?php

namespace Tailgate\Application\Command\Group;

class SubmitScoreForGroupCommand
{
    private $groupId;
    private $userId;
    private $gameId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;

    public function __construct(
        string $groupId,
        string $userId,
        string $gameId,
        int $homeTeamPrediction,
        int $awayTeamPrediction
    ) {
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
    }

    public function getGroupId()
    {
        return $this->groupId;
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
}
