<?php

namespace Tailgate\Test\Domain\Service\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\GameDeleted;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\Season\DeleteGameHandler;

class DeleteGameHandlerTest extends TestCase
{
    private $seasonId = 'seasonId';
    private $gameId;
    private $name = 'name';
    private $sport = Season::SPORT_FOOTBALL;
    private $seasonType = Season::SEASON_TYPE_REG;
    private $seasonStart;
    private $seasonEnd;
    private $season;
    private $deleteGameCommand;

    public function setUp()
    {
        $this->seasonStart = '2019-09-01';
        $this->seasonEnd = '2019-12-28';

        // create a season, add a game, and clear events
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd
        );
        $this->season->addGame(
            TeamId::fromString('homeTeamId'),
            TeamId::fromString('awayTeamId'),
            '2019-10-01',
            '19:30'
        );
        $this->gameId = (string)$this->season->getGames()[0]->getGameId();
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

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(0))->method('assert')->willReturn(true);

        $deleteGameHandler = new DeleteGameHandler($validator, $seasonRepository);

        $deleteGameHandler->handle($this->deleteGameCommand);
    }
}
