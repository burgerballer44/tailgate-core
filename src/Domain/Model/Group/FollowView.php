<?php

namespace Tailgate\Domain\Model\Group;

class FollowView
{
    private $groupId;
    private $followId;
    private $teamId;
    private $groupName;
    private $teamDesignation;
    private $teamMascot;

    public function __construct($groupId, $followId, $teamId, $name, $designation, $mascot)
    {
        $this->groupId = $groupId;
        $this->followId = $followId;
        $this->teamId = $teamId;
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
