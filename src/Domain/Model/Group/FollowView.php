<?php

namespace Tailgate\Domain\Model\Group;

class FollowView
{
    private $groupId;
    private $followId;
    private $teamId;
    private $seasonId;
    private $groupName;
    private $teamDesignation;
    private $teamMascot;

    public function __construct($groupId, $followId, $teamId, $seasonId, $name, $designation, $mascot)
    {
        $this->groupId = $groupId;
        $this->followId = $followId;
        $this->teamId = $teamId;
        $this->seasonId = $seasonId;
        $this->groupName = $name;
        $this->teamDesignation = $designation;
        $this->teamMascot = $mascot;
    }

    public function getGroupId()
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

    public function getGroupName()
    {
        return $this->groupName;
    }

    public function getTeamDesignation()
    {
        return $this->teamDesignation;
    }

    public function getTeamMascot()
    {
        return $this->teamMascot;
    }
}
