<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class MemberId implements IdentifiesAggregate
{
    private $memberId;

    public function __construct($memberId = null)
    {
        $this->memberId = null === $memberId ? Uuid::uuid4()->toString() : $memberId;
    }

    public static function fromString($memberId)
    {
        return new MemberId($memberId);
    }

    public function __toString()
    {
        return (string) $this->memberId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof MemberId
            && $this->memberId === $other->memberId
        ;
    }
}
