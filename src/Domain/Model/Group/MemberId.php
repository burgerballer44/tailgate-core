<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Burger\Aggregate\IdentifiesAggregate;

class MemberId implements IdentifiesAggregate
{
    private $memberId;

    public function __construct($memberId = null)
    {
        $this->memberId = null === $memberId ? Uuid::uuid4()->toString() : $memberId;
    }

    public static function fromString($memberId) : IdentifiesAggregate
    {
        return new MemberId($memberId);
    }

    public function __toString() : string
    {
        return (string) $this->memberId;
    }

    public function equals(IdentifiesAggregate $other) : bool
    {
        return
            $other instanceof MemberId
            && $this->memberId === $other->memberId
        ;
    }
}
