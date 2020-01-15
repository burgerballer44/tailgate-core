<?php

namespace Tailgate\Application\Query\Season;

class AllFollowedGamesQuery
{
    private $followId;

    public function __construct($followId)
    {
        $this->followId = $followId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }
}
