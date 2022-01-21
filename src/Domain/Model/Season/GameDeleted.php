<?php

namespace Tailgate\Domain\Model\Season;

use Tailgate\Domain\Model\DomainEvent;

class GameDeleted implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $gameId;
    private $dateOccurred;

    public function __construct(SeasonId $seasonId, GameId $gameId, $dateOccurred)
    {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription() : string
    {
        return 'Game removed from season.';
    }

    public function getAggregateId()
    {
        return $this->seasonId;
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
