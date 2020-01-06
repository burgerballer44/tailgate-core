<?php

namespace Tailgate\Application\Command\Group;

class DeleteGroupCommand
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
