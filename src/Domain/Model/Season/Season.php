<?php

namespace Tailgate\Domain\Model\Season;

use Burger\Aggregate\IdentifiesAggregate;
use RuntimeException;
use Tailgate\Domain\Model\AbstractEventBasedEntity;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Common\TimeOrString;
use Tailgate\Domain\Model\Team\TeamId;

class Season extends AbstractEventBasedEntity
{
    private $seasonId;
    private $sport;
    private $seasonType;
    private $name;
    private $seasonStart;
    private $seasonEnd;
    private $games = [];

    public function getSeasonId()
    {
        return $this->seasonId;
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

    // create an empty season
    protected static function createEmptyEntity(IdentifiesAggregate $seasonId)
    {
        return new static();
    }

    // create a season
    public static function create(
        SeasonId $seasonId,
        $name,
        Sport $sport,
        SeasonType $seasonType,
        DateOrString $seasonStart,
        DateOrString $seasonEnd,
        Date $dateOccurred
    ) {
        $season = new static();

        $season->applyAndRecordThat(
            new SeasonCreated($seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd, $dateOccurred)
        );

        return $season;
    }

    // update the season information
    public function update(Sport $sport, SeasonType $seasonType, $name, DateOrString $seasonStart, DateOrString $seasonEnd, Date $dateOccurred)
    {
        $this->applyAndRecordThat(
            new SeasonUpdated($this->seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd, $dateOccurred)
        );
    }

    // adds a game
    public function addGame(TeamId $homeTeamId, TeamId $awayTeamId, DateOrString $startDate, TimeOrString $startTime, Date $dateOccurred)
    {
        $this->applyAndRecordThat(
            new GameAdded($this->seasonId, new GameId(), $homeTeamId, $awayTeamId, $startDate, $startTime, $dateOccurred)
        );
    }

    // update the score of a game
    public function updateGameScore(GameId $gameId, $homeTeamScore, $awayTeamScore, DateOrString $startDate, TimeOrString $startTime, Date $dateOccurred)
    {
        if (! $this->getGameById($gameId)) {
            throw new RuntimeException('The game does not exist. Cannot update the game score.');
        }

        // scores should be numeric or null
        $homeTeamScore = is_numeric($homeTeamScore) ? $homeTeamScore : null;
        $awayTeamScore = is_numeric($awayTeamScore) ? $awayTeamScore : null;

        $this->applyAndRecordThat(new GameScoreUpdated($this->seasonId, $gameId, $homeTeamScore, $awayTeamScore, $startDate, $startTime, $dateOccurred));
    }

    // delete all games in the season
    public function delete(Date $dateOccurred)
    {
        $this->applyAndRecordThat(new SeasonDeleted($this->seasonId, $dateOccurred));
    }

    // delete a game
    public function deleteGame(GameId $gameId, Date $dateOccurred)
    {
        if (! $this->getGameById($gameId)) {
            throw new RuntimeException('The game does not exist. Cannot delete the game.');
        }

        $this->applyAndRecordThat(new GameDeleted($this->seasonId, $gameId, $dateOccurred));
    }

    protected function applySeasonCreated(SeasonCreated $event)
    {
        $this->seasonId = $event->getAggregateId();
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
            return ! $game->getGameId()->equals($event->getGameId());
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
}
