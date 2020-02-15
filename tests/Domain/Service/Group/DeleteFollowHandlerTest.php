<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\DeleteFollowCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\FollowDeleted;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\DeleteFollowHandler;

class DeleteFollowHandlerTest extends TestCase
{
    private $followId = '';
    private $groupId = 'groupId';
    private $teamId = 'teamId';
    private $seasonId = 'seasonId';
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $deleteFollowCommand;
    private $group;

    public function setUp(): void
    {
        // create a group and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            'groupName',
            'code',
            UserId::fromString('userId')
        );

        // add a follow
        $this->group->followTeam(TeamId::fromString($this->teamId), SeasonId::fromString($this->seasonId));
        $this->group->clearRecordedEvents();

        $this->followId = (string) $this->group->getFollow()->getFollowId();

        $this->deleteFollowCommand = new DeleteFollowCommand($this->groupId, $this->followId);
    }

    public function testItAttemptsToAddAFollowDeletedEventToTheGroupRepository()
    {
        $groupId = $this->groupId;
        $followId = $this->followId;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the FollowDeleted event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $followId) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof FollowDeleted
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getFollowId()->equals(FollowId::fromString($followId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->exactly(0))->method('assert')->willReturn(true);

        $this->deleteFollowHandler = new DeleteFollowHandler($validator, $groupRepository);

        $this->deleteFollowHandler->handle($this->deleteFollowCommand);
    }
}
