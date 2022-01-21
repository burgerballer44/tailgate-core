<?php

namespace Tailgate\Domain\Model\Season;

use Ramsey\Uuid\Uuid;
use Burger\Aggregate\IdentifiesAggregate;

class SeasonId implements IdentifiesAggregate
{
    private $seasonId;

    public function __construct($seasonId = null)
    {
        $this->seasonId = null === $seasonId ? Uuid::uuid4()->toString() : $seasonId;
    }

    public static function fromString($seasonId) : IdentifiesAggregate
    {
        return new SeasonId($seasonId);
    }

    public function __toString() : string
    {
        return (string) $this->seasonId;
    }

    public function equals(IdentifiesAggregate $other) : bool
    {
        return
            $other instanceof SeasonId
            && $this->seasonId === $other->seasonId
        ;
    }
}
