<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Command\Group\AddPlayerToGroupHandler;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\PlayerAdded;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class AddPlayerToGroupHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $memberId = 'memberId';
    private $username = 'username';
    private $group;
    private $addPlayerToGroupCommand;

    public function setUp()
    {
        // create a group and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            $this->groupName,
            UserId::fromString($this->userId)
        );
        $this->memberId = (string) $this->group->getMembers()[0]->getMemberId();
        $this->group->clearRecordedEvents();

        $this->addPlayerToGroupCommand = new AddPlayerToGroupCommand(
            $this->groupId,
            $this->memberId,
            $this->username
        );
    }

    public function testItAddsAPlayerAddedEventToAGroupInTheGroupRepository()
    {
        $groupId = $this->groupId;
        $memberId = $this->memberId;
        $username = $this->username;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the PlayerAdded event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $memberId, $username) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof PlayerAdded
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getPlayerId() instanceof PlayerId
                && $events[0]->getMemberId()->equals(MemberId::fromString($memberId))
                && $events[0]->getUsername() == $username
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));
        
        $addPlayerToGroupHandler = new AddPlayerToGroupHandler($groupRepository);

        $addPlayerToGroupHandler->handle($this->addPlayerToGroupCommand);
    }
}
