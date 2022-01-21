<?php

namespace Tailgate\Domain\Service\Clock;

use DateTimeImmutable;
use Tailgate\Domain\Service\Clock\Clock;

class ClockUsingSystemClock implements Clock
{
    public function currentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}