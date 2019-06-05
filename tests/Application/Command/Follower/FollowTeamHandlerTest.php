<?php

namespace Tailgate\Test\Application\Command\Follower;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Follower\FollowTeamCommand;
use Tailgate\Application\Command\Follower\FollowTeamHandler;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Follower\FollowerId;
use Tailgate\Domain\Model\Follower\TeamFollowed;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Infrastructure\Persistence\Repository\FollowerRepository;

class FollowTeamHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $teamId = 'teamId';
    private $followTeamCommand;

    public function setUp()
    {
        $this->followTeamCommand = new FollowTeamCommand(
            $this->groupId,
            $this->teamId
        );
    }

    public function testItAttemptsToAddATeamFollowedEventToTheFollowerRepository()
    {
        $groupId = $this->groupId;
        $teamId = $this->teamId;

        // only needs the add method
        $followerRepository = $this->getMockBuilder(FollowerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        // the add method should be called once
        // the follower object should have the TeamFollowed event
        $followerRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($follower) use (
                $groupId,
                $teamId
            ) {
                $events = $follower->getRecordedEvents();

                return $events[0] instanceof TeamFollowed
                && $events[0]->getAggregateId() instanceof FollowerId
                && $events[0]->getGroupId()->equals(GroupId::fromString($groupId))
                && $events[0]->getTeamId()->equals(TeamId::fromString($teamId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $this->followTeamHandler = new FollowTeamHandler(
            $followerRepository
        );

        $this->followTeamHandler->handle($this->followTeamCommand);
    }
}
