<?php

namespace Tailgate\Test\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Team\DeleteTeamCommand;
use Tailgate\Application\Command\Team\DeleteTeamHandler;
use Tailgate\Domain\Model\Team\TeamDeleted;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;

class DeleteTeamHandlerTest extends TestCase
{
    private $teamId = 'teamId';
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $deleteTeamCommand;
    private $team;

    public function setUp()
    {
        $this->team = Team::create(TeamId::fromString($this->teamId), $this->designation, $this->mascot);
        $this->team->clearRecordedEvents();

        $this->deleteTeamCommand = new DeleteTeamCommand(
            $this->teamId
        );
    }

    public function testItAttemptsToAddATeamDeletedEventToTheTeamRepository()
    {
        $teamId = $this->teamId;
        $team = $this->team;

        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $teamRepository
           ->expects($this->once())
           ->method('get')
           ->willReturn($team);

        // the add method should be called once
        // the team object should have the TeamDeleted event
        $teamRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(
                function ($team) use ($teamId) {
                    $events = $team->getRecordedEvents();

                    return $events[0] instanceof TeamDeleted
                && $events[0]->getAggregateId()->equals(TeamId::fromString($teamId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
                }
        ));

        $this->deleteTeamHandler = new DeleteTeamHandler(
            $teamRepository
        );

        $this->deleteTeamHandler->handle($this->deleteTeamCommand);
    }
}
