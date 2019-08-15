<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class ScoreId implements IdentifiesAggregate
{
    private $scoreId;

    public function __construct($scoreId = null)
    {
        $this->scoreId = null === $scoreId ? Uuid::uuid4()->toString() : $scoreId;
    }

    public static function fromString($scoreId)
    {
        return new ScoreId($scoreId);
    }

    public function __toString()
    {
        return (string) $this->scoreId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof ScoreId
            && $this->scoreId === $other->scoreId
        ;
    }
}
