<?php

namespace Tailgate\Domain\Service\Clock;

use Tailgate\Domain\Service\Clock\Clock;
use DateTimeImmutable;

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