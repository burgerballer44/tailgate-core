<?php

namespace Tailgate\Domain\Model\Sport;

use Ramsey\Uuid\Uuid;
use Buttercup\Protects\IdentifiesAggregate;

class SportId implements IdentifiesAggregate
{
    private $sportId;

    public function __construct($sportId = null) 
    {
        $this->sportId = null === $sportId ? Uuid::uuid4()->toString() : $sportId;
    }

    public static function fromString($sportId)
    {
        return new SportId($sportId);
    }

    public function __toString()
    {
        return (string) $this->sportId;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return
            $other instanceof SportId
            && $this->sportId === $other->sportId
        ;
    }
}