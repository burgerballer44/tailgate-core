<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Command\Group\DeleteScoreHandler;
use Tailgate\Domain\Model\Group\ScoreDeleted;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class DeleteScoreHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $playerId = '';
    private $scoreId;
    private $groupName = 'groupName';
    private $groupInviteCode = 'code';
    private $group;
    private $deleteScoreCommand;

    public function setUp()
    {
        // create a game, add a score, and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            $this->groupName,
            $this->groupInviteCode,
            UserId::fromString($this->userId)
        );
        $memberId = $this->group->getMembers()[0]->getMemberId();
        $this->group->addPlayer($memberId, 'username');
        $playerId = $this->group->getPlayers()[0]->getPlayerId();
        $this->group->submitScore($playerId, GameId::fromString('gameID'), 70, 60);
        $this->scoreId = (string) $this->group->getScores()[0]->getScoreId();
        $this->group->clearRecordedEvents();

        $this->deleteScoreCommand = new DeleteScoreCommand(
            $this->groupId,
            $this->scoreId
        );
    }

    public function testItAddsAScoreDeletedEventToTheGroupRepository()
    {
        $groupId = $this->groupId;
        $scoreId = $this->scoreId;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the ScoreDeleted event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $scoreId) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof ScoreDeleted
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getScoreId()->equals(ScoreId::fromString($scoreId))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $deleteScoreHandler = new DeleteScoreHandler($groupRepository);

        $deleteScoreHandler->handle($this->deleteScoreCommand);
    }
}
