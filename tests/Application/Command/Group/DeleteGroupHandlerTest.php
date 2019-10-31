<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Application\Command\Group\DeleteGroupHandler;
use Tailgate\Domain\Model\Group\GroupDeleted;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class DeleteGroupHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $groupInviteCode = 'code';
    private $group;
    private $deleteGroupCommand;

    public function setUp()
    {
        // create a group and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            $this->groupName,
            $this->groupInviteCode,
            UserId::fromString($this->userId)
        );
        $this->group->clearRecordedEvents();

        $this->deleteGroupCommand = new DeleteGroupCommand(
            $this->groupId
        );
    }

    public function testItAddsAGroupDeletedEventToTheGroupRepository()
    {
        $groupId = $this->groupId;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the GroupDeleted event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof GroupDeleted
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $deleteGroupHandler = new DeleteGroupHandler($groupRepository);

        $deleteGroupHandler->handle($this->deleteGroupCommand);
    }
}
