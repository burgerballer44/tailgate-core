<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Application\Command\Group\DeletePlayerHandler;
use Tailgate\Domain\Model\Group\PlayerDeleted;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class DeletePlayerHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $playerId;
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $group;
    private $deletePlayerCommand;

    public function setUp()
    {
        // create a group, add a member, and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            $this->groupName,
            UserId::fromString($this->userId)
        );
        $memberId = $this->group->getMembers()[0]->getMemberId();
        $this->group->addPlayer($memberId, 'username');
        $this->playerId = (string) $this->group->getPlayers()[0]->getPlayerId();

        $this->group->clearRecordedEvents();

        $this->deletePlayerCommand = new DeletePlayerCommand(
            $this->groupId,
            $this->playerId
        );
    }

    public function testItAddsAPlayerDeletedEventToTheGroupRepository()
    {
        $groupId = $this->groupId;
        $playerId = $this->playerId;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the PlayerDeleted event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $playerId) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof PlayerDeleted
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getPlayerId()->equals(PlayerId::fromString($playerId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $deletePlayerHandler = new DeletePlayerHandler($groupRepository);

        $deletePlayerHandler->handle($this->deletePlayerCommand);
    }
}