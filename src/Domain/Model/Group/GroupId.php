<?php

namespace Tailgate\Domain\Model\Group;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class GroupId implements IdentifiesAggregate
{
    private $groupId;

    public function __construct($groupId = null) 
    {
        $this->groupId = null === $groupId ? Uuid::uuid4()->toString() : $groupId;
    }

    public static function fromString($groupId)
    {
        return new GroupId($groupId);
    }

    public function __toString()
    {
        return (string) $this->groupId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof GroupId
            && $this->groupId === $other->groupId
        ;
    }
}