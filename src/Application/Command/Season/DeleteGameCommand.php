<?php

namespace Tailgate\Application\Command\Season;

class DeleteGameCommand
{
    private $seasonId;
    private $gameId;

    public function __construct(string $seasonId, string $gameId)
    {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getGameId()
    {
        return $this->gameId;
    }
}
