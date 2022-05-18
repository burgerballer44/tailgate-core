<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Season\GameId;

interface ScoreViewRepositoryInterface
{
    public function get(ScoreId $id);

    public function getAllByGroup(GroupId $id);

    public function getAllByGroupPlayer(GroupId $id, PlayerId $playerId);

    public function getAllByGroupGame(GroupId $id, GameId $gameId);
}
