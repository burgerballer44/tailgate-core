<?php

namespace Tailgate\Domain\Model\User;

use Ramsey\Uuid\Uuid;
use Burger\Aggregate\IdentifiesAggregate;

class UserId implements IdentifiesAggregate
{
    private $userId;

    public function __construct($userId = null)
    {
        $this->userId = null === $userId ? Uuid::uuid4()->toString() : $userId;
    }

    public static function fromString($userId) : IdentifiesAggregate
    {
        return new UserId($userId);
    }

    public function __toString() : string
    {
        return (string) $this->userId;
    }

    public function equals(IdentifiesAggregate $other) : bool
    {
        return
            $other instanceof UserId
            && $this->userId === $other->userId
        ;
    }
}
