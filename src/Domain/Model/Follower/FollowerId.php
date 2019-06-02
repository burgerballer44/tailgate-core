<?php

namespace Tailgate\Domain\Model\Follower;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class FollowerId implements IdentifiesAggregate
{
    private $followerId;

    public function __construct($followerId = null) 
    {
        $this->followerId = null === $followerId ? Uuid::uuid4()->toString() : $followerId;
    }

    public static function fromString($followerId)
    {
        return new FollowerId($followerId);
    }

    public function __toString()
    {
        return (string) $this->followerId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof FollowerId
            && $this->followerId === $other->followerId
        ;
    }
}