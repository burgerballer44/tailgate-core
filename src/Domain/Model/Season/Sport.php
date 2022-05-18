<?php

namespace Tailgate\Domain\Model\Season;

use InvalidArgumentException;

class Sport
{
    public const FOOTBALL = 'Football';
    public const BASKETBALL = 'Basketball';

    private $value;

    private function __construct(string $value)
    {
        // check if a key was passed in
        if (in_array($value, $this->getValidKeys())) {
            $this->value = $this->getSports()[$value]['title'];

            return;
        }

        // check if a title was passed in
        if (in_array($value, $this->getValidTitles())) {
            $this->value = $value;

            return;
        }

        throw new InvalidArgumentException("Invalid sport. Sport does not exist.");
    }

    public static function fromString(string $value): Sport
    {
        return new Sport($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(Sport $other): bool
    {
        return $other instanceof Sport && $this->value === $other->value;
    }

    public static function getValidKeys()
    {
        return array_column(self::getSports(), 'key');
    }

    public static function getValidTitles()
    {
        return array_column(self::getSports(), 'title');
    }

    public static function getFootball(): Sport
    {
        return new Sport(self::FOOTBALL);
    }

    public static function getBasketball(): Sport
    {
        return new Sport(self::BASKETBALL);
    }

    public static function getSports()
    {
        return [
            self::FOOTBALL => [
                'key' => self::FOOTBALL,
                'title' => "Football",
                'description' => "",
            ],
            self::BASKETBALL => [
                'key' => self::BASKETBALL,
                'title' => "Basketball",
                'description' => "",
            ],
        ];
    }
}
