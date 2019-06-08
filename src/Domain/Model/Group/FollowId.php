<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class FollowId implements IdentifiesAggregate
{
    private $followId;

    public function __construct($followId = null) 
    {
        $this->followId = null === $followId ? Uuid::uuid4()->toString() : $followId;
    }

    public static function fromString($followId)
    {
        return new FollowId($followId);
    }

    public function __toString()
    {
        return (string) $this->followId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof FollowId
            && $this->followId === $other->followId
        ;
    }
}