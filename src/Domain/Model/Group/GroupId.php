<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Burger\Aggregate\IdentifiesAggregate;

class GroupId implements IdentifiesAggregate
{
    private $groupId;

    public function __construct($groupId = null)
    {
        $this->groupId = null === $groupId ? Uuid::uuid4()->toString() : $groupId;
    }

    public static function fromString($groupId) : IdentifiesAggregate
    {
        return new GroupId($groupId);
    }

    public function __toString() : string
    {
        return (string) $this->groupId;
    }

    public function equals(IdentifiesAggregate $other) : bool
    {
        return
            $other instanceof GroupId
            && $this->groupId === $other->groupId
        ;
    }
}
