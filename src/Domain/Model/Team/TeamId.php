<?php

namespace Tailgate\Domain\Model\Team;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class TeamId implements IdentifiesAggregate
{
    private $teamId;

    public function __construct($teamId = null)
    {
        $this->teamId = null === $teamId ? Uuid::uuid4()->toString() : $teamId;
    }

    public static function fromString($teamId)
    {
        return new TeamId($teamId);
    }

    public function __toString()
    {
        return (string) $this->teamId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof TeamId
            && $this->teamId === $other->teamId
        ;
    }
}
