<?php

namespace Tailgate\Domain\Model\Team;

use Burger\Aggregate\IdentifiesAggregate;
use Ramsey\Uuid\Uuid;

class TeamId implements IdentifiesAggregate
{
    private $teamId;

    public function __construct($teamId = null)
    {
        $this->teamId = null === $teamId ? Uuid::uuid4()->toString() : $teamId;
    }

    public static function fromString($teamId): IdentifiesAggregate
    {
        return new TeamId($teamId);
    }

    public function __toString(): string
    {
        return (string) $this->teamId;
    }

    public function equals(IdentifiesAggregate $other): bool
    {
        return
            $other instanceof TeamId
            && $this->teamId === $other->teamId
        ;
    }
}
