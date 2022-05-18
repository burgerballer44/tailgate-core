<?php

namespace Tailgate\Domain\Model\Season;

use Burger\Aggregate\IdentifiesAggregate;
use Ramsey\Uuid\Uuid;

class GameId implements IdentifiesAggregate
{
    private $gameId;

    public function __construct($gameId = null)
    {
        $this->gameId = null === $gameId ? Uuid::uuid4()->toString() : $gameId;
    }

    public static function fromString($gameId): IdentifiesAggregate
    {
        return new GameId($gameId);
    }

    public function __toString(): string
    {
        return (string) $this->gameId;
    }

    public function equals(IdentifiesAggregate $other): bool
    {
        return
            $other instanceof GameId
            && $this->gameId === $other->gameId
        ;
    }
}
