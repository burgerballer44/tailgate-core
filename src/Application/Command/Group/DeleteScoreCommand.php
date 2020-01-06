<?php

namespace Tailgate\Application\Command\Group;

class DeleteScoreCommand
{
    private $groupId;
    private $scoreId;

    public function __construct($groupId, $scoreId)
    {
        $this->groupId = $groupId;
        $this->scoreId = $scoreId;
    }

    public function getgroupId()
    {
        return $this->groupId;
    }

    public function getScoreId()
    {
        return $this->scoreId;
    }
}
