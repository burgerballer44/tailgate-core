<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\ChangePlayerOwnerCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\PlayerOwnerChanged;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\ChangePlayerOwnerHandler;

class ChangePlayerOwnerHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $member = 'MemberId';
    private $group;
    private $playerId;
    private $newMemberId;
    private $changePlayerOwnerCommand;

    public function setUp()
    {
        // create a group and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            'groupName',
            'invitecode',
            UserId::fromString($this->userId)
        );
        $this->group->addMember(UserId::fromString('otherMember'));
        $memberId = $this->group->getMembers()[0]->getMemberId();
        $this->newMemberId = (string) $this->group->getMembers()[1]->getMemberId();
        $this->group->addPlayer($memberId, 'username');
        $this->group->clearRecordedEvents();

        $this->playerId = (string) $this->group->getPlayers()[0]->getPlayerId();

        $this->changePlayerOwnerCommand = new ChangePlayerOwnerCommand(
            $this->groupId,
            (string)$this->playerId,
            (string)$this->newMemberId,
        );
    }

    public function testItAddsAPlayerOwnerChangedEventToAGroupInTheGroupRepository()
    {
        $groupId = $this->groupId;
        $newMemberId = $this->newMemberId;
        $playerId = $this->playerId;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the PlayerOwnerChanged event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $newMemberId, $playerId) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof PlayerOwnerChanged
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getMemberId()->equals(MemberId::fromString($newMemberId))
                && $events[0]->getPlayerId()->equals(PlayerId::fromString($playerId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);
        
        $changeplayerOwnerHandler = new ChangePlayerOwnerHandler($validator, $groupRepository);

        $changeplayerOwnerHandler->handle($this->changePlayerOwnerCommand);
    }
}
