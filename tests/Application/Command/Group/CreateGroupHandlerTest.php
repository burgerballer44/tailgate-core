<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Command\Group\CreateGroupHandler;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Repository\GroupRepository;

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

        // only needs the add method
        $groupRepository = $this->getMockBuilder(GroupRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        // the add method should be called once
        // the group object should have the GroupCreated event
        $groupRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($group) use (
                $groupName,
                $ownerId
            ) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof GroupCreated
                && $events[0]->getAggregateId() instanceof GroupId
                && $events[0]->getName() === $groupName
                && $events[0]->getOwnerId()->equals(UserId::fromString($ownerId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $createGroupHandler = new CreateGroupHandler(
            $groupRepository
        );

        $createGroupHandler->handle($this->createGroupCommand);
    }
}
