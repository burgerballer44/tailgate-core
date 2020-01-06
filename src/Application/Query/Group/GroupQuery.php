<?php

namespace Tailgate\Application\Query\Group;

class GroupQuery
{
    private $groupId;

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}
