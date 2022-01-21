<?php

namespace Tailgate\Domain\Model\Common;

use DateTimeImmutable;
use InvalidArgumentException;

class Date
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private $date;

    private function __construct(string $date)
    {
        if ( ! DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $date)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid date provided: %s. Expected format is: %s',
                    $date,
                    self::DATE_FORMAT
                )
            );
        }

        $this->date = $date;
    }

    public static function fromString(string $date) : self
    {
        return self::fromDateTimeImmutable(new DateTimeImmutable($date));
    }

    public static function fromDateTimeImmutable(DateTimeImmutable $dateTimeImmutable) : self
    {
        return new self($dateTimeImmutable->format(self::DATE_FORMAT));
    }

    public function __toString() : string
    {
        return (string) $this->date;
    }
}