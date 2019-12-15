<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Application\Command\Season\UpdateGameScoreHandler;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameScoreUpdated;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class UpdateGameScoreHandlerTest extends TestCase
{
    private $homeTeamScore = 70;
    private $awayTeamScore = 60;
    private $startDate;
    private $startTime;

    private $seasonId = 'seasonId';
    private $homeTeamId = 'homeTeamId';
    private $awayTeamId = 'awayTeamId';

    private $name = 'name';
    private $sport = Season::SPORT_FOOTBALL;
    private $seasonType = Season::SEASON_TYPE_REG;
    private $seasonStart;
    private $seasonEnd;

    private $season;
    private $game;
    private $updateGameScoreCommand;

    public function setUp()
    {
        // create a season, add a game, and clear events
        $this->seasonStart = '2019-09-01';
        $this->seasonEnd = '2019-12-28';
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd
        );
        $this->season->addGame(
            TeamId::fromString($this->homeTeamId),
            TeamId::fromString($this->awayTeamId),
            '2019-10-01',
            '19:30'
        );
        $this->season->clearRecordedEvents();
        $games = $this->season->getGames();
        $this->game = $games[0];

        $this->startDate = '2019-12-01';
        $this->startTime = '19:30';
        $this->updateGameScoreCommand = new UpdateGameScoreCommand(
            SeasonId::fromString($this->seasonId),
            $this->game->getGameId(),
            $this->homeTeamScore,
            $this->awayTeamScore,
            $this->startDate,
            $this->startTime,
        );
    }

    public function testItAddsAGameScoreUpdatedEventToTheSeasonRepository()
    {
        $homeTeamScore = $this->homeTeamScore;
        $awayTeamScore = $this->awayTeamScore;
        $startDate = $this->startDate;
        $startTime = $this->startTime;
        $season = $this->season;
        $seasonId = $this->seasonId;
        $game = $this->game;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $seasonRepository->expects($this->once())->method('get')->willReturn($season);

        // the add method should be called once
        // the season object should have the GameScoreUpdated event
        $seasonRepository->expects($this->once())->method('add')->with($this->callback(
            function ($season) use ($homeTeamScore, $awayTeamScore, $game, $seasonId, $startDate, $startTime) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof GameScoreUpdated
                && $events[0]->getAggregateId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getGameId()->equals($game->getGameId())
                && $events[0]->getHomeTeamScore() === $homeTeamScore
                && $events[0]->getAwayTeamScore() === $awayTeamScore
                && $events[0]->getStartDate() === \DateTimeImmutable::createFromFormat('Y-m-d', $startDate)->format('Y-m-d H:i:s')
                && $events[0]->getStartTime() === \DateTimeImmutable::createFromFormat('H:i', $startTime)->format('Y-m-d H:i:s')
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $updateGameScoreHandler = new UpdateGameScoreHandler($seasonRepository);

        $updateGameScoreHandler->handle($this->updateGameScoreCommand);
    }
}
