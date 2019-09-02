<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class PlayerId implements IdentifiesAggregate
{
    private $playerId;

    public function __construct($playerId = null)
    {
        $this->playerId = null === $playerId ? Uuid::uuid4()->toString() : $playerId;
    }

    public static function fromString($playerId)
    {
        return new PlayerId($playerId);
    }

    public function __toString()
    {
        return (string) $this->playerId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof PlayerId
            && $this->playerId === $other->playerId
        ;
    }
}
