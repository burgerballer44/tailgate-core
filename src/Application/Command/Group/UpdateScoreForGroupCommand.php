<?php

namespace Tailgate\Application\Command\Group;

class UpdateScoreForGroupCommand
{
    private $groupId;
    private $scoreId;
    private $homeTeamPrediction;
    private $awayTeamPrediction;

    public function __construct($groupId, $scoreId, $homeTeamPrediction, $awayTeamPrediction)
    {
        $this->groupId = $groupId;
        $this->scoreId = $scoreId;
        $this->homeTeamPrediction = $homeTeamPrediction;
        $this->awayTeamPrediction = $awayTeamPrediction;
    }

    public function getGroupId()
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
}
