<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Command\Group\CreateGroupHandler;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class CreateGroupHandlerTest extends TestCase
{
    private $groupName = 'groupName';
    private $ownerId = 'ownerId';
    private $createGroupCommand;

    public function setUp()
    {
        $this->createGroupCommand = new CreateGroupCommand(
            $this->groupName,
            $this->ownerId
        );
    }

    public function testItAddsAGroupCreatedEventToTheGroupRepository()
    {
        $groupName = $this->groupName;
        $ownerId = $this->ownerId;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the nextIdentity method should be called once and will return a new GroupId
        $groupRepository->expects($this->once())->method('nextIdentity')->willReturn(new GroupId());

        // the add method should be called once
        // the group object should have the GroupCreated event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupName, $ownerId) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof GroupCreated
                && $events[0]->getAggregateId() instanceof GroupId
                && $events[0]->getName() === $groupName
                && $events[0]->getOwnerId()->equals(UserId::fromString($ownerId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $createGroupHandler = new CreateGroupHandler($groupRepository);

        $createGroupHandler->handle($this->createGroupCommand);
    }
}
