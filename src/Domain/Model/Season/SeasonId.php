<?php

namespace Tailgate\Domain\Model\Season;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class SeasonId implements IdentifiesAggregate
{
    private $seasonId;

    public function __construct($seasonId = null)
    {
        $this->seasonId = null === $seasonId ? Uuid::uuid4()->toString() : $seasonId;
    }

    public static function fromString($seasonId)
    {
        return new SeasonId($seasonId);
    }

    public function __toString()
    {
        return (string) $this->seasonId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof SeasonId
            && $this->seasonId === $other->seasonId
        ;
    }
}
