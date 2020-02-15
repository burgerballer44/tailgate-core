<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\MemberUpdated;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\UpdateMemberHandler;

class UpdateMemberHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $groupInviteCode = 'code';
    private $groupRole = Group::G_ROLE_ADMIN;
    private $allowMultiplePlayers = 'pizza';
    private $group;
    private $memberId;
    private $updateMemberCommand;

    public function setUp(): void
    {
        // create a group and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            $this->groupName,
            $this->groupInviteCode,
            UserId::fromString($this->userId)
        );
        $this->memberId = (string) $this->group->getMembers()[0]->getMemberId();
        $this->group->clearRecordedEvents();

        $this->updateMemberCommand = new UpdateMemberCommand(
            $this->groupId,
            $this->memberId,
            $this->groupRole,
            $this->allowMultiplePlayers
        );
    }

    public function testItAddsAMemberUpdatedEventToAGroupInTheGroupRepository()
    {
        $groupId = $this->groupId;
        $memberId = $this->memberId;
        $groupRole = $this->groupRole;
        $allowMultiplePlayers = $this->allowMultiplePlayers;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the MemberUpdated event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $memberId, $groupRole, $allowMultiplePlayers) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof MemberUpdated
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getMemberId()->equals(MemberId::fromString($memberId))
                && $events[0]->getGroupRole() == $groupRole
                && $events[0]->getAllowMultiplePlayers() == $allowMultiplePlayers
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);
        
        $updateMemberHandler = new UpdateMemberHandler($validator, $groupRepository);

        $updateMemberHandler->handle($this->updateMemberCommand);
    }
}
