<?php

namespace Tailgate\Test\Domain\Model\Season;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\Game;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Team\TeamId;

class SeasonTest extends TestCase
{
    private $seasonId;
    private $sport = 'sport';
    private $seasonType = 'seasonType';
    private $name = 'name';
    private $seasonStart;
    private $seasonEnd;

    public function setUp()
    {
        $this->seasonId = SeasonId::fromString('seasonId');
        $this->seasonStart = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-09-01');
        $this->seasonEnd = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-12-28');
    }

    public function testSeasonShouldBeTheSameAfterReconstitution()
    {
        $season = Season::create(
            $this->seasonId,
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $events = $season->getRecordedEvents();
        $season->clearRecordedEvents();

        $reconstitutedSeason = Season::reconstituteFrom(
            new AggregateHistory($this->seasonId, (array) $events)
        );

        $this->assertEquals(
            $season,
            $reconstitutedSeason,
            'the reconstituted season does not match the original season'
        );
    }

    public function testASeasonCanBeCreated()
    {
        $season = Season::create(
            $this->seasonId,
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );

        $this->assertEquals($this->seasonId, $season->getId());
        $this->assertEquals($this->sport, $season->getSport());
        $this->assertEquals($this->seasonType, $season->getSeasonType());
        $this->assertEquals($this->name, $season->getName());
        $this->assertEquals($this->seasonStart, $season->getSeasonStart());
        $this->assertEquals($this->seasonEnd, $season->getSeasonEnd());
    }

    public function testAGameIsAddedWhenGameIsAdded()
    {
        $season = Season::create(
            $this->seasonId,
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01');

        $season->addGame($homeTeamId, $awayTeamId, $startDate);
        $games = $season->getGames();

        $this->assertCount(1, $games);
        $this->assertTrue($games[0] instanceof Game);
        $this->assertTrue($games[0]->getGameId() instanceof GameId);
        $this->assertTrue($games[0]->getHomeTeamId()->equals($homeTeamId));
        $this->assertTrue($games[0]->getAwayTeamId()->equals($awayTeamId));
        $this->assertEquals($startDate, $games[0]->getStartDate());
        $this->assertTrue($games[0]->getSeasonId()->equals($this->seasonId));
    }

    public function testAGameScoreIsUpdatedToAGameWhenGameScoreIsUpdated()
    {
        $season = Season::create(
            $this->seasonId,
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01');
        $season->addGame($homeTeamId, $awayTeamId, $startDate);
        $games = $season->getGames();

        $homeTeamScore = 70;
        $awayTeamScore = 60;

        $season->updateGameScore(
            $games[0]->getGameId(),
            $homeTeamScore,
            $awayTeamScore
        );

        $this->assertCount(1, $games);
        $this->assertTrue($games[0] instanceof Game);
        $this->assertTrue($games[0]->getGameId() instanceof GameId);
        $this->assertEquals($homeTeamScore, $games[0]->getHomeTeamScore());
        $this->assertEquals($awayTeamScore, $games[0]->getAwayTeamScore());
        $this->assertTrue($games[0]->getStartDate() === $startDate);
        $this->assertTrue($games[0]->getSeasonId()->equals($this->seasonId));
    }

    public function testAGameCanBeRemovedFromASeason()
    {
        // create a season and add three games
        $season = Season::create(
            $this->seasonId,
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01');

        $season->addGame($homeTeamId, $awayTeamId, $startDate);
        $season->addGame($homeTeamId, $awayTeamId, $startDate);
        $season->addGame($homeTeamId, $awayTeamId, $startDate);
        $games = $season->getGames();
        $this->assertCount(3, $games);

        $gameId1 = $games[0]->getGameId();
        $gameId2 = $games[1]->getGameId();
        $gameId3 = $games[2]->getGameId();

        $season->deleteGame($gameId2);

        $games = $season->getGames();

        $this->assertCount(2, $games);
        $this->assertTrue($games[0]->getGameId()->equals($gameId1));
        $this->assertTrue($games[1]->getGameId()->equals($gameId3));
    }

    public function testThereAreNoGamesInADeletedSeason()
    {
        // create a season and add a game
        $season = Season::create(
            $this->seasonId,
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $homeTeamId = TeamId::fromString('homeTeamId');
        $awayTeamId = TeamId::fromString('awayTeamId');
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01');

        $season->addGame($homeTeamId, $awayTeamId, $startDate);
        $games = $season->getGames();
        $this->assertCount(1, $games);

        $season->delete();

        $games = $season->getGames();

        $this->assertCount(0, $games);
    }

    public function testASeasonCanBeUpdated()
    {
        $sport = 'updatedsport';
        $seasonType = 'updatedseasonType';
        $name = 'updatedname';
        $seasonStart = \DateTimeImmutable::createFromFormat('Y-m-d', '2021-12-28');
        $seasonEnd = \DateTimeImmutable::createFromFormat('Y-m-d', '2021-12-28');
        $season = Season::create(
            $this->seasonId,
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );

        $season->update($sport, $seasonType, $name, $seasonStart, $seasonEnd);

        $this->assertEquals($sport, $season->getSport());
        $this->assertEquals($seasonType, $season->getSeasonType());
        $this->assertEquals($name, $season->getName());
        $this->assertEquals($seasonStart, $season->getSeasonStart());
        $this->assertEquals($seasonEnd, $season->getSeasonEnd());
    }
}
