<?php

namespace Tailgate\Application\Command\Group;

class CreateGroupCommand
{
    private $name;
    private $ownerId;

    public function __construct($name, $ownerId)
    {
        $this->name = $name;
        $this->ownerId = $ownerId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
    }
}
