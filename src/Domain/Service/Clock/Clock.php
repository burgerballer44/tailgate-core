<?php

namespace Tailgate\Domain\Service\Clock;

use DateTimeImmutable;

interface Clock
{
    public function currentTime() : DateTimeImmutable;
}
