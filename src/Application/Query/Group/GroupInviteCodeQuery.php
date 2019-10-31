<?php

namespace Tailgate\Application\Query\Group;

class GroupInviteCodeQuery
{
    private $inviteCode;

    public function __construct(string $inviteCode)
    {
        $this->inviteCode = $inviteCode;
    }

    public function getInviteCode()
    {
        return $this->inviteCode;
    }
}
