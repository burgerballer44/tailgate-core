<?php

namespace Tailgate\Test\Domain\Service\Season;

use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonType;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Season\CreateSeasonHandler;
use Tailgate\Test\BaseTestCase;

class CreateSeasonHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->seasonId = SeasonId::fromString('seasonId');
        $this->name = 'name';
        $this->sport = Sport::getFootball();
        $this->seasonType = SeasonType::getRegularSeason();
        $this->seasonStart = DateOrString::fromString('2019-09-01');
        $this->seasonEnd = DateOrString::fromString('2019-12-28');

        $this->createSeasonCommand = new CreateSeasonCommand(
            $this->name,
            $this->sport,
            $this->seasonType,
            $this->seasonStart,
            $this->seasonEnd
        );
    }

    public function testItAddsASeasonCreatedEventToTheSeasonRepository()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $seasonRepository = $this->getMockBuilder(SeasonRepositoryInterface::class)->getMock();
        $seasonRepository->expects($this->once())->method('nextIdentity')->willReturn(new SeasonId());
        $seasonRepository->expects($this->once())->method('add');

        $createSeasonHandler = new CreateSeasonHandler($validator, new FakeClock(), $seasonRepository);

        $createSeasonHandler->handle($this->createSeasonCommand);
    }
}
