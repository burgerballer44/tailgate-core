<?php

namespace Tailgate\Domain\Model\Group;

use Burger\Aggregate\IdentifiesAggregate;
use Ramsey\Uuid\Uuid;

class PlayerId implements IdentifiesAggregate
{
    private $playerId;

    public function __construct($playerId = null)
    {
        $this->playerId = null === $playerId ? Uuid::uuid4()->toString() : $playerId;
    }

    public static function fromString($playerId): IdentifiesAggregate
    {
        return new PlayerId($playerId);
    }

    public function __toString(): string
    {
        return (string) $this->playerId;
    }

    public function equals(IdentifiesAggregate $other): bool
    {
        return
            $other instanceof PlayerId
            && $this->playerId === $other->playerId
        ;
    }
}
