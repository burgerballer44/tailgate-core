<?php

namespace Tailgate\Domain\Service\Clock;

use DateTimeImmutable;

class ClockUsingSystemClock implements Clock
{
    public function currentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}
