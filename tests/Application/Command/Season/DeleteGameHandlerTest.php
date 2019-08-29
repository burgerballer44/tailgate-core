<?php

namespace Tailgate\Test\Application\Command\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Application\Command\Season\DeleteGameHandler;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameDeleted;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class DeleteGameHandlerTest extends TestCase
{
    private $seasonId = 'seasonId';
    private $gameId;
    private $sport = 'sport';
    private $seasonType = 'seasonType';
    private $name = 'name';
    private $seasonStart;
    private $seasonEnd;
    private $season;
    private $deleteGameCommand;

    public function setUp()
    {
        $this->seasonStart = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-09-01');
        $this->seasonEnd = \DateTimeImmutable::createFromFormat('Y-m-d', '2019-12-28');

        // create a season, add a game, and clear events
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            $this->sport,
            $this->seasonType,
            $this->name,
            $this->seasonStart,
            $this->seasonEnd
        );
        $this->season->addGame(
            TeamId::fromString('homeTeamId'),
            TeamId::fromString('awayTeamId'),
            \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01')
        );
        $this->gameId = $this->season->getGames()[0]->getGameId();
        $this->season->clearRecordedEvents();

        $this->deleteGameCommand = new DeleteGameCommand(
            $this->seasonId,
            $this->gameId
        );
    }

    public function testItAddsAGameDeletedEventToTheSeasonRepository()
    {
        $seasonId = $this->seasonId;
        $gameId = $this->gameId;
        $season = $this->season;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $seasonRepository->expects($this->once())->method('get')->willReturn($season);

        // the add method should be called once
        // the season object should have the GameDeleted event
        $seasonRepository->expects($this->once())->method('add')->with($this->callback(
            function ($season) use ($seasonId, $gameId) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof GameDeleted
                && $events[0]->getAggregateId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getGameId() instanceof GameId
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $deleteGameHandler = new DeleteGameHandler($seasonRepository);

        $deleteGameHandler->handle($this->deleteGameCommand);
    }
}
