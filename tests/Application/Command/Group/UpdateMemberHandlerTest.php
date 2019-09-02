<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Command\Group\UpdateMemberHandler;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberUpdated;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class UpdateMemberHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $groupRole = 'groupRole';
    private $group;
    private $memberId;
    private $updateMemberCommand;

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

        $this->updateMemberCommand = new UpdateMemberCommand(
            $this->groupId,
            $this->memberId,
            $this->groupRole
        );
    }

    public function testItAddsAMemberUpdatedEventToAGroupInTheGroupRepository()
    {
        $groupId = $this->groupId;
        $memberId = $this->memberId;
        $groupRole = $this->groupRole;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the MemberUpdated event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $memberId, $groupRole) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof MemberUpdated
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getMemberId()->equals(MemberId::fromString($memberId))
                && $events[0]->getGroupRole() == $groupRole
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));
        
        $updateMemberHandler = new UpdateMemberHandler($groupRepository);

        $updateMemberHandler->handle($this->updateMemberCommand);
    }
}
