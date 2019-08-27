<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\DomainEvent;

class GameDeleted implements DomainEvent, SeasonDomainEvent
{
    private $seasonId;
    private $gameId;
    private $occurredOn;

    public function __construct(SeasonId $seasonId, GameId $gameId)
    {
        $this->seasonId = $seasonId;
        $this->gameId = $gameId;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->seasonId;
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
