<?php

namespace Tailgate\Test\Domain\Service\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Season\DeleteSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonDeleted;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\Season\DeleteSeasonHandler;

class DeleteSeasonHandlerTest extends TestCase
{
    private $seasonId = 'seasonId';
    private $name = 'name';
    private $sport = Season::SPORT_FOOTBALL;
    private $seasonType = Season::SEASON_TYPE_REG;
    private $seasonStart;
    private $seasonEnd;
    private $season;
    private $deleteSeasonCommand;

    public function setUp()
    {
        // create season and clear events
        $this->seasonStart = '2021-09-01';
        $this->seasonEnd = '2021-12-28';
        $this->season = Season::create(
            SeasonId::fromString($this->seasonId),
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd
        );
        $this->season->clearRecordedEvents();

        $this->deleteSeasonCommand = new DeleteSeasonCommand(
            SeasonId::fromString($this->seasonId)
        );
    }

    public function testItAddsASeasonDeletedEventToTheSeasonRepository()
    {
        $seasonId = $this->seasonId;
        $season = $this->season;

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();

        // the nextIdentity method should be called once and will return a the season
        $seasonRepository->expects($this->once())->method('get')->willReturn($season);

        // the add method should be called once
        // the season object should have the SeasonDeleted event
        $seasonRepository->expects($this->once())->method('add')->with($this->callback(
            function ($season) use ($seasonId) {
                $events = $season->getRecordedEvents();

                return $events[0] instanceof SeasonDeleted
                && $events[0]->getAggregateId()->equals(SeasonId::fromString($seasonId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(0))->method('assert')->willReturn(true);

        $deleteSeasonHandler = new DeleteSeasonHandler($validator, $seasonRepository);

        $deleteSeasonHandler->handle($this->deleteSeasonCommand);
    }
}
