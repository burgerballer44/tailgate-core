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

    private $seasonId = 'seasonId';
    private $homeTeamId = 'homeTeamId';
    private $awayTeamId = 'awayTeamId';

    private $sport = 'sport';
    private $seasonType = 'seasonType';
    private $name = 'name';
    private $seasonStart;
    private $seasonEnd;

    private $season;
    private $game;
    private $updateGameScoreCommand;

    public function setUp()
    {
        // create a season, add a game, and clear events
        $this->seasonStart = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-09-01');
        $this->seasonEnd = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-12-28');
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $this->season->addGame(
            TeamId::fromString($this->homeTeamId),
            TeamId::fromString($this->awayTeamId),
            \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01')
        );
        $this->season->clearRecordedEvents();
        $games = $this->season->getGames();
        $this->game = $games[0];

        $this->updateGameScoreCommand = new UpdateGameScoreCommand(
            SeasonId::fromString($this->seasonId),
            $this->game->getGameId(),
            $this->homeTeamScore,
            $this->awayTeamScore
        );
    }

    public function testItAddsAGameScoreUpdatedEventToTheSeasonRepository()
    {
        $homeTeamScore = $this->homeTeamScore;
        $awayTeamScore = $this->awayTeamScore;
        $season = $this->season;
        $seasonId = $this->seasonId;
        $game = $this->game;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $seasonRepository->expects($this->once())->method('get')->willReturn($season);

        // the add method should be called once
        // the season object should have the GameScoreUpdated event
        $seasonRepository->expects($this->once())->method('add')->with($this->callback(
            function ($season) use ($homeTeamScore, $awayTeamScore, $game, $seasonId) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof GameScoreUpdated
                && $events[0]->getAggregateId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getGameId()->equals($game->getGameId())
                && $events[0]->getHomeTeamScore() === $homeTeamScore
                && $events[0]->getAwayTeamScore() === $awayTeamScore
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $updateGameScoreHandler = new UpdateGameScoreHandler($seasonRepository);

        $updateGameScoreHandler->handle($this->updateGameScoreCommand);
    }
}
