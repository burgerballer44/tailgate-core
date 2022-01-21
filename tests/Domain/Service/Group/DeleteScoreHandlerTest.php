<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreDeleted;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\DeleteScoreHandler;
use Tailgate\Test\BaseTestCase;

class DeleteScoreHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->userId = UserId::fromString('userId');
        $this->groupName = 'groupName';
        $this->groupInviteCode = GroupInviteCode::create();
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        // create a group and clear events
        $this->group = Group::create(
            $this->groupId,
            $this->groupName,
            $this->groupInviteCode,
            $this->userId,
            $this->dateOccurred
        );
        $memberId = $this->group->getMembers()[0]->getMemberId();
        $this->group->addPlayer($memberId, 'username', $this->dateOccurred);
        $playerId = $this->group->getPlayers()[0]->getPlayerId();
        $this->group->submitScore($playerId, GameId::fromString('gameID'), 70, 60, $this->dateOccurred);
        $this->scoreId = (string) $this->group->getScores()[0]->getScoreId();
        $this->group->clearRecordedEvents();

        $this->deleteScoreCommand = new DeleteScoreCommand(
            $this->groupId,
            $this->scoreId
        );
    }

    public function testItAddsAScoreDeletedEventToTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $deleteScoreHandler = new DeleteScoreHandler($groupRepository, new FakeClock());

        $deleteScoreHandler->handle($this->deleteScoreCommand);
    }
}
