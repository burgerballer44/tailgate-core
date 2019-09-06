<?php

namespace Tailgate\Domain\Model\Group;

class GroupView
{
    private $groupId;
    private $name;
    private $ownerId;
    private $members = [];
    private $players = [];
    private $scores = [];

    public function __construct($groupId, $name, $ownerId)
    {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->ownerId = $ownerId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
    }

    public function getMembers()
    {
        return $this->members;
    }

    public function getPlayers()
    {
        return $this->players;
    }

    public function getScores()
    {
        return $this->scores;
    }

    public function addMemberViews($memberView)
    {
        if (is_array($memberView)) {
            $this->members = $memberView;
        } else {
            $this->members[] = $memberView;
        }
    }

    public function addPlayerViews($playerView)
    {
        if (is_array($playerView)) {
            $this->players = $playerView;
        } else {
            $this->players[] = $playerView;
        }
    }

    public function addScoreViews($scoreView)
    {
        if (is_array($scoreView)) {
            $this->scores = $scoreView;
        } else {
            $this->scores[] = $scoreView;
        }
    }
}
