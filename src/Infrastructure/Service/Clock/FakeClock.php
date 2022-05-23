<?php

namespace Tailgate\Infrastructure\Service\Clock;

use DateTimeImmutable;
use Tailgate\Domain\Service\Clock\Clock;

class FakeClock implements Clock
{
    private $currentTime;

    public function setCurrentTime(DateTimeImmutable $currentTime)
    {
        $this->currentTime = $currentTime;
    }

    public function currentTime(): DateTimeImmutable
    {
        if ($this->currentTime === null) {
            return new DateTimeImmutable('now');
        }

        return $this->currentTime;
    }
}
