<?php

namespace Tailgate\Test\Domain\Service\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Team\UpdateTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamUpdated;
use Tailgate\Domain\Service\Team\UpdateTeamHandler;

class UpdateTeamHandlerTest extends TestCase
{
    private $teamId = 'teamId';
    private $designation = 'designationUpdated';
    private $mascot = 'mascotUpdated';
    private $updateTeamCommand;
    private $team;

    public function setUp()
    {
        $this->team = Team::create(TeamId::fromString($this->teamId), 'designation', 'mascot');
        $this->team->clearRecordedEvents();

        $this->updateTeamCommand = new UpdateTeamCommand($this->teamId, $this->designation, $this->mascot);
    }

    public function testItAttemptsToAddATeamUpdatedEventToTheTeamRepository()
    {
        $teamId = $this->teamId;
        $designation = $this->designation;
        $mascot = $this->mascot;
        $team = $this->team;

        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the team
        $teamRepository->expects($this->once())->method('get')->willReturn($team);

        // the add method should be called once
        // the team object should have the TeamUpdated event
        $teamRepository->expects($this->once())->method('add')->with($this->callback(
            function ($team) use ($teamId, $designation, $mascot) {
                $events = $team->getRecordedEvents();

                return $events[0] instanceof TeamUpdated
                && $events[0]->getAggregateId()->equals(TeamId::fromString($teamId))
                && $events[0]->getDesignation() === $designation
                && $events[0]->getMascot() === $mascot
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $this->updateTeamHandler = new UpdateTeamHandler($validator, $teamRepository);

        $this->updateTeamHandler->handle($this->updateTeamCommand);
    }
}
