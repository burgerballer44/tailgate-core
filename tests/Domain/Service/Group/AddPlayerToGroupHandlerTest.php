<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\PlayerAdded;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\AddPlayerToGroupHandler;

class AddPlayerToGroupHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $groupInviteCode = 'code';
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
            $this->groupInviteCode,
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
        
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $addPlayerToGroupHandler = new AddPlayerToGroupHandler($validator, $groupRepository);

        $addPlayerToGroupHandler->handle($this->addPlayerToGroupCommand);
    }
}
