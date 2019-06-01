<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Game\GameId;

class Score
{
    private $scoreId;
    private $name;
    private $ownerId;
    private $scores = [];
    private $recordedEvents = [];

    private function __construct(
        $scoreId,
        $groupId,
        $userId,
        $gameId,
        $homeTeamPrediction,
        $awayTeamPrediction
    ) {
        $this->scoreId = $scoreId;
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->gameId = $gameId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction; 
    }

    public static function create(
        ScoreId $scoreId,
        GroupId $groupId,
        UserId $userId,
        GameId $gameId,
        $homeTeamPrediction,
        $awayTeamPrediction
    ) {
        $newScore = new Score(
            $scoreId,
            $groupId,
            $userId,
            $gameId,
            $homeTeamPrediction,
            $awayTeamPrediction
        );

        return $newScore;
    }
}
