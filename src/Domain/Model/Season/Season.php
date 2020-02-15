<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\IdentifiesAggregate;
use RuntimeException;
use Tailgate\Domain\Model\AbstractEntity;
use Tailgate\Domain\Model\Team\TeamId;

class Season extends AbstractEntity
{
    const SPORT_FOOTBALL = 'Football';
    const SPORT_BASKETBALL = 'Basketball';

    const SEASON_TYPE_REG = 'Regular-Season';

    private $seasonId;
    private $sport;
    private $seasonType;
    private $name;
    private $seasonStart;
    private $seasonEnd;
    private $games = [];

    protected function __construct($seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd)
    {
        $this->seasonId = $seasonId;
        $this->sport = $sport;
        $this->seasonType = $seasonType;
        $this->name = $name;
        $this->seasonStart = $seasonStart;
        $this->seasonEnd = $seasonEnd;
    }

    // create a season
    public static function create(
        SeasonId $seasonId,
        $name,
        $sport,
        $seasonType,
        $seasonStart,
        $seasonEnd
    ) {
        if (!in_array($sport, self::getValidSports())) {
            throw new RuntimeException('Invalid sport. Sport does not exist.');
        }

        if (!in_array($seasonType, self::getValidSeasonTypes())) {
            throw new RuntimeException('Invalid season type. Season type does not exist.');
        }

        // seasonStart and seasonEnd will be a date most of the time but can be a string sometimes
        $seasonStartDateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $seasonStart);
        $seasonStart = $seasonStartDateTime instanceof \DateTimeImmutable ? $seasonStartDateTime->format('Y-m-d H:i:s') : $seasonStart;
        $seasonEndDateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $seasonEnd);
        $seasonEnd = $seasonEndDateTime instanceof \DateTimeImmutable ? $seasonEndDateTime->format('Y-m-d H:i:s') : $seasonEnd;

        $newSeason = new Season($seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd);

        $newSeason->recordThat(
            new SeasonCreated($seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd)
        );

        return $newSeason;
    }

    // create an empty season
    protected static function createEmptyEntity(IdentifiesAggregate $seasonId)
    {
        return new Season($seasonId, '', '', '', '', '');
    }

    public function getId()
    {
        return (string) $this->seasonId;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function getSeasonType()
    {
        return $this->seasonType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSeasonStart()
    {
        return $this->seasonStart;
    }

    public function getSeasonEnd()
    {
        return $this->seasonEnd;
    }

    public function getGames()
    {
        return $this->games;
    }

    // update the season information
    public function update($sport, $seasonType, $name, $seasonStart, $seasonEnd)
    {
        if (!in_array($sport, $this->getValidSports())) {
            throw new RuntimeException('Invalid sport. Sport does not exist.');
        }

        if (!in_array($seasonType, $this->getValidSeasonTypes())) {
            throw new RuntimeException('Invalid season type. Season type does not exist.');
        }

        // seasonStart and seasonEnd will be a date most of the time but can be a string sometimes
        $seasonStartDateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $seasonStart);
        $seasonStart = $seasonStartDateTime instanceof \DateTimeImmutable ? $seasonStartDateTime->format('Y-m-d H:i:s') : $seasonStart;
        $seasonEndDateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $seasonEnd);
        $seasonEnd = $seasonEndDateTime instanceof \DateTimeImmutable ? $seasonEndDateTime->format('Y-m-d H:i:s') : $seasonEnd;

        $this->applyAndRecordThat(
            new SeasonUpdated($this->seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd)
        );
    }

    // adds a game
    public function addGame(TeamId $homeTeamId, TeamId $awayTeamId, $startDate, $startTime)
    {
        // startDate and startTime will be a date most of the time but can be a string sometimes
        $startDateDateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $startDate);
        $startDate = $startDateDateTime instanceof \DateTimeImmutable ? $startDateDateTime->format('Y-m-d H:i:s') : $startDate;
        $startTimeDateTime = \DateTimeImmutable::createFromFormat('H:i', $startTime);
        $startTime = $startTimeDateTime instanceof \DateTimeImmutable ? $startTimeDateTime->format('Y-m-d H:i:s') : $startTime;

        $this->applyAndRecordThat(
            new GameAdded($this->seasonId, new GameId(), $homeTeamId, $awayTeamId, $startDate, $startTime)
        );
    }

    // update the score of a game
    public function updateGameScore(GameId $gameId, $homeTeamScore, $awayTeamScore, $startDate, $startTime)
    {
        if (!$this->getGameById($gameId)) {
            throw new RuntimeException('The game does not exist. Cannot update the game score.');
        }

        // startDate and startTime will be a date most of the time but can be a string sometimes
        $startDateDateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $startDate);
        $startDate = $startDateDateTime instanceof \DateTimeImmutable ? $startDateDateTime->format('Y-m-d H:i:s') : $startDate;
        $startTimeDateTime = \DateTimeImmutable::createFromFormat('H:i', $startTime);
        $startTime = $startTimeDateTime instanceof \DateTimeImmutable ? $startTimeDateTime->format('Y-m-d H:i:s') : $startTime;

        // scores should be numeric or null
        $homeTeamScore = is_numeric($homeTeamScore) ? $homeTeamScore : null;
        $awayTeamScore = is_numeric($awayTeamScore) ? $awayTeamScore : null;

        $this->applyAndRecordThat(new GameScoreUpdated($this->seasonId, $gameId, $homeTeamScore, $awayTeamScore, $startDate, $startTime));
    }

    // delete all games in the season
    public function delete()
    {
        $this->applyAndRecordThat(new SeasonDeleted($this->seasonId));
    }

    // delete a game
    public function deleteGame(GameId $gameId)
    {
        if (!$this->getGameById($gameId)) {
            throw new RuntimeException('The game does not exist. Cannot delete the game.');
        }

        $this->applyAndRecordThat(new GameDeleted($this->seasonId, $gameId));
    }

    protected function applySeasonCreated(SeasonCreated $event)
    {
        $this->sport = $event->getSport();
        $this->seasonType = $event->getSeasonType();
        $this->name = $event->getName();
        $this->seasonStart = $event->getSeasonStart();
        $this->seasonEnd = $event->getSeasonEnd();
    }

    protected function applySeasonUpdated(SeasonUpdated $event)
    {
        $this->sport = $event->getSport();
        $this->seasonType = $event->getSeasonType();
        $this->name = $event->getName();
        $this->seasonStart = $event->getSeasonStart();
        $this->seasonEnd = $event->getSeasonEnd();
    }

    protected function applyGameAdded(GameAdded $event)
    {
        $this->games[] = Game::create(
            $event->getAggregateId(),
            $event->getGameId(),
            $event->getHomeTeamId(),
            $event->getAwayTeamId(),
            $event->getStartDate(),
            $event->getStartTime()
        );
    }

    protected function applyGameScoreUpdated(GameScoreUpdated $event)
    {
        $game = $this->getGameById($event->getGameId());
        $game->addHomeTeamScore($event->getHomeTeamScore());
        $game->addAwayTeamScore($event->getAwayTeamScore());
        $game->addStartDate($event->getStartDate());
        $game->addStartTime($event->getStartTime());
    }


    protected function applyGameDeleted(GameDeleted $event)
    {
        $this->games = array_values(array_filter($this->games, function ($game) use ($event) {
            return !$game->getGameId()->equals($event->getGameId());
        }));
    }

    protected function applySeasonDeleted()
    {
        $this->games = [];
    }

    private function getGameById(GameId $gameId)
    {
        foreach ($this->games as $game) {
            if ($game->getGameId()->equals($gameId)) {
                return $game;
            }
        }
    }

    public static function getValidSports()
    {
        return [
            self::SPORT_FOOTBALL,
            self::SPORT_BASKETBALL,
        ];
    }

    public static function getValidSeasonTypes()
    {
        return [
            self::SEASON_TYPE_REG,
        ];
    }
}
