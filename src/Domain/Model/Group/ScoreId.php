<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Burger\Aggregate\IdentifiesAggregate;

class ScoreId implements IdentifiesAggregate
{
    private $scoreId;

    public function __construct($scoreId = null)
    {
        $this->scoreId = null === $scoreId ? Uuid::uuid4()->toString() : $scoreId;
    }

    public static function fromString($scoreId) : IdentifiesAggregate
    {
        return new ScoreId($scoreId);
    }

    public function __toString() : string
    {
        return (string) $this->scoreId;
    }

    public function equals(IdentifiesAggregate $other) : bool
    {
        return
            $other instanceof ScoreId
            && $this->scoreId === $other->scoreId
        ;
    }
}
