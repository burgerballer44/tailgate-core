<?php

namespace Tailgate\Domain\Model\Season;

use Buttercup\Protects\IdentifiesAggregate;
use Tailgate\Domain\Model\AbstractEntity;
use Tailgate\Domain\Model\Team\TeamId;

class Season extends AbstractEntity
{
    const SPORT_FOOTBALL = 10;
    const SPORT_BASKETBALL = 20;

    const SEASON_TYPE_PRE = 10;
    const SEASON_TYPE_REG = 20;
    const SEASON_TYPE_POST = 30;

    private $seasonId;
    private $sport;
    private $seasonType;
    private $name;
    private $seasonStart;
    private $seasonEnd;
    private $games;

    protected function __construct(
        $seasonId,
        $sport,
        $seasonType,
        $name,
        $seasonStart,
        $seasonEnd
    ) {
        $this->seasonId = $seasonId;
        $this->sport = $sport;
        $this->seasonType = $seasonType;
        $this->name = $name;
        $this->seasonStart = $seasonStart;
        $this->seasonEnd = $seasonEnd;
    }

    public static function create(
        SeasonId $seasonId,
        $sport,
        $seasonType,
        $name,
        \DateTimeImmutable $seasonStart,
        \DateTimeImmutable $seasonEnd
    ) {
        $newSeason = new Season(
            $seasonId,
            $sport,
            $seasonType,
            $name,
            $seasonStart,
            $seasonEnd
        );

        $newSeason->recordThat(
            new SeasonCreated(
                $seasonId,
                $sport,
                $seasonType,
                $name,
                $seasonStart,
                $seasonEnd
            )
        );

        return $newSeason;
    }

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

    public function addGame(TeamId $homeTeamId, TeamId $awayTeamId, \DateTimeImmutable $startDate)
    {
        $this->applyAndRecordThat(
            new GameAdded(
                new GameId(),
                $this->seasonId,
                $homeTeamId,
                $awayTeamId,
                $startDate
            )
        );
    }

    public function addGameScore(GameId $gameId, $homeTeamScore, $awayTeamScore)
    {
        $this->applyAndRecordThat(
            new GameScoreAdded(
                $gameId,
                $this->seasonId,
                $homeTeamScore,
                $awayTeamScore
            )
        );
    }

    protected function applySeasonCreated(SeasonCreated $event)
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
            $event->getGameId(),
            $event->getSeasonId(),
            $event->getHomeTeamId(),
            $event->getAwayTeamId(),
            $event->getStartDate()
        );
    }

    protected function applyGameScoreAdded(GameScoreAdded $event)
    {   
        $game = $this->getGameById($event->getGameId());
        $game->addHomeTeamScore($event->getHomeTeamScore());
        $game->addAwayTeamScore($event->getAwayTeamScore());
    }

    private function getGameById(GameId $gameId)
    {
        foreach ($this->games as $game) {
            if ($game->getGameId() == $gameId) {
                return $game;
            }
        }
        // Todo
        // throw notfoundexception
    }
}
