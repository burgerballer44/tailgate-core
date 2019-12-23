<?php

namespace Tailgate\Test\Domain\Service\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamDeleted;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Service\Team\DeleteTeamHandler;

class DeleteTeamHandlerTest extends TestCase
{
    private $teamId = 'teamId';
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $deleteTeamCommand;
    private $team;

    public function setUp()
    {
        // create a team and clear events
        $this->team = Team::create(TeamId::fromString($this->teamId), $this->designation, $this->mascot);
        $this->team->clearRecordedEvents();

        $this->deleteTeamCommand = new DeleteTeamCommand($this->teamId);
    }

    public function testItAttemptsToAddATeamDeletedEventToTheTeamRepository()
    {
        $teamId = $this->teamId;
        $team = $this->team;

        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $teamRepository->expects($this->once())->method('get')->willReturn($team);

        // the add method should be called once
        // the team object should have the TeamDeleted event
        $teamRepository->expects($this->once())->method('add')->with($this->callback(
            function ($team) use ($teamId) {
                $events = $team->getRecordedEvents();

                return $events[0] instanceof TeamDeleted
                && $events[0]->getAggregateId()->equals(TeamId::fromString($teamId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(0))->method('assert')->willReturn(true);

        $this->deleteTeamHandler = new DeleteTeamHandler($validator, $teamRepository);

        $this->deleteTeamHandler->handle($this->deleteTeamCommand);
    }
}
