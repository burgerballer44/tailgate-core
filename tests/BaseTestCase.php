<?php

namespace Tailgate\Test;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Service\Clock\FakeClock;

class BaseTestCase extends TestCase
{
    protected function getFakeTime($time = 'now')
    {
        $clock = new FakeClock();
        $currentTime = new DateTimeImmutable($time);
        $clock->setCurrentTime($currentTime);

        return $clock;
    }
}
