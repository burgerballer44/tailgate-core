<?php

namespace Tailgate\Domain\Model\User;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class UserId implements IdentifiesAggregate
{
    private $userId;

    public function __construct($userId = null)
    {
        $this->userId = null === $userId ? Uuid::uuid4()->toString() : $userId;
    }

    public static function fromString($userId)
    {
        return new UserId($userId);
    }

    public function __toString()
    {
        return (string) $this->userId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof UserId
            && $this->userId === $other->userId
        ;
    }
}
