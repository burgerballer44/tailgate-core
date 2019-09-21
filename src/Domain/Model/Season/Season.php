<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\IdentifiesAggregate;
use Tailgate\Domain\Model\AbstractEntity;
use Tailgate\Domain\Model\ModelException;
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

    /**
     * create a season
     * @param  SeasonId           $seasonId    [description]
     * @param  [type]             $sport       [description]
     * @param  [type]             $seasonType  [description]
     * @param  [type]             $name        [description]
     * @param  \DateTimeImmutable $seasonStart [description]
     * @param  \DateTimeImmutable $seasonEnd   [description]
     * @return [type]                          [description]
     */
    public static function create(
        SeasonId $seasonId,
        $name,
        $sport,
        $seasonType,
        \DateTimeImmutable $seasonStart,
        \DateTimeImmutable $seasonEnd
    ) {
        if (!in_array($sport, self::getValidSports())) {
            throw new ModelException('Invalid sport. Sport does not exist.');
        }

        if (!in_array($seasonType, self::getValidSeasonTypes())) {
            throw new ModelException('Invalid season type. Season type does not exist.');
        }

        $newSeason = new Season($seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd);

        $newSeason->recordThat(
            new SeasonCreated($seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd)
        );

        return $newSeason;
    }

    /**
     * create an empty season
     * @param  IdentifiesAggregate $seasonId [description]
     * @return [type]                        [description]
     */
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

    /**
     * update the season information
     * @param  [type]             $sport       [description]
     * @param  [type]             $seasonType  [description]
     * @param  [type]             $name        [description]
     * @param  \DateTimeImmutable $seasonStart [description]
     * @param  \DateTimeImmutable $seasonEnd   [description]
     * @return [type]                          [description]
     */
    public function update($sport, $seasonType, $name, \DateTimeImmutable $seasonStart, \DateTimeImmutable $seasonEnd)
    {
        if (!in_array($sport, $this->getValidSports())) {
            throw new ModelException('Invalid sport. Sport does not exist.');
        }

        if (!in_array($seasonType, $this->getValidSeasonTypes())) {
            throw new ModelException('Invalid season type. Season type does not exist.');
        }

        $this->applyAndRecordThat(
            new SeasonUpdated($this->seasonId, $sport, $seasonType, $name, $seasonStart, $seasonEnd)
        );
    }

    /**
     * adds a game
     * @param TeamId             $homeTeamId [description]
     * @param TeamId             $awayTeamId [description]
     * @param \DateTimeImmutable $startDate  [description]
     */
    public function addGame(TeamId $homeTeamId, TeamId $awayTeamId, \DateTimeImmutable $startDate)
    {
        $this->applyAndRecordThat(
            new GameAdded($this->seasonId, new GameId(), $homeTeamId, $awayTeamId, $startDate)
        );
    }

    /**
     * [updateGameScore description]
     * @param  GameId $gameId        [description]
     * @param  [type] $homeTeamScore [description]
     * @param  [type] $awayTeamScore [description]
     * @return [type]                [description]
     */
    public function updateGameScore(GameId $gameId, $homeTeamScore, $awayTeamScore)
    {
        if (!$game = $this->getGameById($gameId)) {
            throw new ModelException('The game does not exist. Cannot update the game score.');
        }

        $this->applyAndRecordThat(new GameScoreUpdated($this->seasonId, $gameId, $homeTeamScore, $awayTeamScore));
    }

    /**
     * delete all games in the season
     * @return [type] [description]
     */
    public function delete()
    {
        $this->applyAndRecordThat(new SeasonDeleted($this->seasonId));
    }

    /**
     * delete a game
     * @param  GameId $gameId [description]
     * @return [type]         [description]
     */
    public function deleteGame(GameId $gameId)
    {
        if (!$game = $this->getGameById($gameId)) {
            throw new ModelException('The game does not exist. Cannot delete the game.');
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
            $event->getStartDate()
        );
    }

    protected function applyGameScoreUpdated(GameScoreUpdated $event)
    {
        $game = $this->getGameById($event->getGameId());
        $game->addHomeTeamScore($event->getHomeTeamScore());
        $game->addAwayTeamScore($event->getAwayTeamScore());
    }


    protected function applyGameDeleted(GameDeleted $event)
    {
        $this->games = array_values(array_filter($this->games, function ($game) use ($event) {
            return !$game->getGameId()->equals($event->getGameId());
        }));
    }

    protected function applySeasonDeleted(SeasonDeleted $event)
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

    /**
     * return valid sports
     * @return [type] [description]
     */
    public static function getValidSports()
    {
        return [
            self::SPORT_FOOTBALL,
            self::SPORT_BASKETBALL,
        ];
    }

    /**
     * return valid season types
     * @return [type] [description]
     */
    public static function getValidSeasonTypes()
    {
        return [
            self::SEASON_TYPE_REG,
        ];
    }
}
