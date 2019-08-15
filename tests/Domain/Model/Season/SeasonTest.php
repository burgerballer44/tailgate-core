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

    public function testAGameScoreIsAddedToAGameWhenGameScoreIsAdded()
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

        $season->addGameScore(
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
}
