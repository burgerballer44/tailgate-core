<?php

namespace Tailgate\Test\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Team\DeleteFollowCommand;
use Tailgate\Application\Command\Team\DeleteFollowHandler;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Domain\Model\Team\FollowDeleted;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;

class DeleteFollowHandlerTest extends TestCase
{
    private $followId = 'followId';
    private $teamId = 'teamId';
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $deleteFollowCommand;
    private $team;

    public function setUp()
    {
        // create a team and clear events
        $this->team = Team::create(TeamId::fromString($this->teamId), $this->designation, $this->mascot);
        $this->team->clearRecordedEvents();

        $this->deleteFollowCommand = new DeleteFollowCommand($this->teamId, $this->followId);
    }

    public function testItAttemptsToAddAFollowDeletedEventToTheTeamRepository()
    {
        $teamId = $this->teamId;
        $followId = $this->followId;
        $team = $this->team;

        $teamRepository = $this->getMockBuilder(TeamRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $teamRepository->expects($this->once())->method('get')->willReturn($team);

        // the add method should be called once
        // the team object should have the FollowDeleted event
        $teamRepository->expects($this->once())->method('add')->with($this->callback(
            function ($team) use ($teamId, $followId) {
                $events = $team->getRecordedEvents();

                return $events[0] instanceof FollowDeleted
                && $events[0]->getAggregateId()->equals(TeamId::fromString($teamId))
                && $events[0]->getFollowId() instanceof FollowId
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $this->deleteFollowHandler = new DeleteFollowHandler($teamRepository);

        $this->deleteFollowHandler->handle($this->deleteFollowCommand);
    }
}
