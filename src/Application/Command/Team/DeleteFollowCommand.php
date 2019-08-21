<?php

namespace Tailgate\Application\Command\Team;

class DeleteFollowCommand
{
    private $teamId;
    private $followId;

    public function __construct(string $teamId, string $followId)
    {
        $this->teamId = $teamId;
        $this->followId = $followId;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getFollowId()
    {
        return $this->followId;
    }
}
