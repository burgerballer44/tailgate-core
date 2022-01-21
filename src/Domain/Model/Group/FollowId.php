<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Burger\Aggregate\IdentifiesAggregate;

class FollowId implements IdentifiesAggregate
{
    private $followId;

    public function __construct($followId = null)
    {
        $this->followId = null === $followId ? Uuid::uuid4()->toString() : $followId;
    }

    public static function fromString($followId) : IdentifiesAggregate
    {
        return new FollowId($followId);
    }

    public function __toString() : string
    {
        return (string) $this->followId;
    }

    public function equals(IdentifiesAggregate $other) : bool
    {
        return
            $other instanceof FollowId
            && $this->followId === $other->followId
        ;
    }
}
