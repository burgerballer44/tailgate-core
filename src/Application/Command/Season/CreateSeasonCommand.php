<?php

namespace Tailgate\Application\Command\Season;

class CreateSeasonCommand
{
    private $sport;
    private $seasonType;
    private $name;
    private $seasonStart;
    private $seasonEnd;

    public function __construct(
        string $name,
        string $sport,
        string $seasonType,
        string $seasonStart,
        string $seasonEnd
    ) {
        $this->name = $name;
        $this->sport = $sport;
        $this->seasonType = $seasonType;
        $this->seasonStart = $seasonStart;
        $this->seasonEnd = $seasonEnd;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function getSeasonType()
    {
        return $this->seasonType;
    }

    public function getSeasonStart()
    {
        return $this->seasonStart;
    }

    public function getSeasonEnd()
    {
        return $this->seasonEnd;
    }
}
