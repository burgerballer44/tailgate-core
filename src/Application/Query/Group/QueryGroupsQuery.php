<?php

namespace Tailgate\Application\Query\Group;

class QueryGroupsQuery
{
    private $userId;
    private $name;

    public function __construct($userId = null, $name = null)
    {
        $this->userId = $userId;
        $this->name = $name;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getName()
    {
        return $this->name;
    }
}
