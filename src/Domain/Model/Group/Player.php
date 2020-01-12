<?php

namespace Tailgate\Domain\Model\Group;

class Player
{
    private $playerId;
    private $memberId;
    private $groupId;
    private $username;

    private function __construct(
        $playerId,
        $memberId,
        $groupId,
        $username
    ) {
        $this->playerId = $playerId;
        $this->memberId = $memberId;
        $this->groupId = $groupId;
        $this->username = $username;
    }

    public static function create(
        GroupId $groupId,
        PlayerId $playerId,
        MemberId $memberId,
        $username
    ) {
        $newPlayer = new Player(
            $playerId,
            $memberId,
            $groupId,
            $username
        );

        return $newPlayer;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function changeMember(MemberId $memberId)
    {
        $this->memberId = $memberId;
    }
}
