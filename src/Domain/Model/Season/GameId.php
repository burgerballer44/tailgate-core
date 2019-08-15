<?php

namespace Tailgate\Domain\Model\Season;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class GameId implements IdentifiesAggregate
{
    private $gameId;

    public function __construct($gameId = null)
    {
        $this->gameId = null === $gameId ? Uuid::uuid4()->toString() : $gameId;
    }

    public static function fromString($gameId)
    {
        return new GameId($gameId);
    }

    public function __toString()
    {
        return (string) $this->gameId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof GameId
            && $this->gameId === $other->gameId
        ;
    }
}
