<?php

namespace Tailgate\Domain\Model\Season;

use InvalidArgumentException;

class SeasonType
{
    const REGULAR = 'Regular-Season';

    private $value;

    private function __construct(string $value)
    {
        // check if a key was passed in
        if (in_array($value, $this->getValidKeys())) {
            $this->value = $this->getSeasonTypes()[$value]['title'];
            return;
        }

        // check if a title was passed in
        if (in_array($value, $this->getValidTitles())) {
            $this->value = $value;
            return;
        }

        throw new InvalidArgumentException("Invalid season type. Season type does not exist.");
    }

    public static function fromString(string $value) : SeasonType
    {
        return new SeasonType($value);
    }

    public function __toString() : string
    {
        return (string) $this->value;
    }

    public function equals(SeasonType $other) : bool
    {
        return $other instanceof SeasonType && $this->value === $other->value;
    }

    public static function getValidKeys()
    {
        return array_column(self::getSeasonTypes(), 'key');
    }

    public static function getValidTitles()
    {
        return array_column(self::getSeasonTypes(), 'title');
    }

    public static function getRegularSeason() : SeasonType
    {
        return new SeasonType(self::REGULAR);
    }

    public static function getSeasonTypes()
    {
        return [
            self::REGULAR => [
                'key' => self::REGULAR,
                'title' => "Regular Season",
                'description' => "",
            ],
        ];
    }
}
