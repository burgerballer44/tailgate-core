<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;

interface ScoreViewRepositoryInterface
{
    public function get(ScoreId $id);
    public function getAllByGroup(GroupId $id);
    public function getAllByGroupUser(GroupId $id, UserId $userId);
    public function getAllByGroupGame(GroupId $id, GameId $gameId);
}
