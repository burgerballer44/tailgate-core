<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\FollowTeamCommand;
use Tailgate\Application\Command\Group\FollowTeamHandler;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\TeamFollowed;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Repository\GroupRepository;

class FollowTeamHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $teamId = 'teamId';
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $followTeamCommand;
    private $group;

    public function setUp()
    {
        $this->group = Group::create(GroupId::fromString($this->groupId), $this->groupName, UserId::fromString($this->userId));
        $this->group->clearRecordedEvents();

        $this->followTeamCommand = new FollowTeamCommand(
            $this->groupId,
            $this->teamId
        );
    }

    public function testItAttemptsToAddATeamFollowedEventToTheGroupRepository()
    {
        $groupId = $this->groupId;
        $teamId = $this->teamId;
        $group = $this->group;

        // only needs the get and add method
        $groupRepository = $this->getMockBuilder(GroupRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'add'])
            ->getMock();

        // the get method should be called once and will return the group
        $groupRepository
           ->expects($this->once())
           ->method('get')
           ->willReturn($group);

        // the add method should be called once
        // the group object should have the TeamFollowed event
        $groupRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($group) use (
                $groupId,
                $teamId
            ) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof TeamFollowed
                && $events[0]->getAggregateId() instanceof GroupId
                && $events[0]->getFollowId() instanceof FollowId
                && $events[0]->getGroupId()->equals(GroupId::fromString($groupId))
                && $events[0]->getTeamId()->equals(TeamId::fromString($teamId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $this->followTeamHandler = new FollowTeamHandler(
            $groupRepository
        );

        $this->followTeamHandler->handle($this->followTeamCommand);
    }
}
