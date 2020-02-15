<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Service\Security\RandomStringInterface;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\CreateGroupHandler;

class CreateGroupHandlerTest extends TestCase
{
    private $groupName = 'groupName';
    private $groupInviteCode = 'code';
    private $ownerId = 'ownerId';
    private $createGroupCommand;

    public function setUp(): void
    {
        $this->createGroupCommand = new CreateGroupCommand(
            $this->groupName,
            $this->ownerId
        );
    }

    public function testItAddsAGroupCreatedEventToTheGroupRepository()
    {
        $groupName = $this->groupName;
        $groupInviteCode = $this->groupInviteCode;
        $ownerId = $this->ownerId;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the nextIdentity method should be called once and will return a new GroupId
        $groupRepository->expects($this->once())->method('nextIdentity')->willReturn(new GroupId());

        // the add method should be called once
        // the group object should have the GroupCreated event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupName, $groupInviteCode, $ownerId) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof GroupCreated
                && $events[0]->getAggregateId() instanceof GroupId
                && $events[0]->getName() === $groupName
                && $events[0]->getInviteCode() === $groupInviteCode
                && $events[0]->getOwnerId()->equals(UserId::fromString($ownerId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $randomStringer = $this->createMock(RandomStringInterface::class);
        $randomStringer->expects($this->once())->method('generate')->willReturn($groupInviteCode);

        $createGroupHandler = new CreateGroupHandler($validator, $groupRepository, $randomStringer);

        $createGroupHandler->handle($this->createGroupCommand);
    }
}
