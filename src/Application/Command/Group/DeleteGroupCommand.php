<?php

namespace Tailgate\Application\Command\Group;

class DeleteGroupCommand
{
    private $groupId;

    public function __construct(string $groupId)
    {
        $this->groupId = $groupId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}
