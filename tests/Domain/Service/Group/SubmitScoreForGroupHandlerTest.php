<?php

namespace Tailgate\Test\Domain\Service\Group;

use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\Group\SubmitScoreForGroupHandler;
use Tailgate\Test\BaseTestCase;

class SubmitScoreForGroupHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->userId = UserId::fromString('userId');
        $this->teamId = TeamId::fromString('teamId');
        $this->seasonId = SeasonId::fromString('seasonId');
        $this->gameId = GameId::fromString('gameId');
        $this->groupName = 'groupName';
        $this->username = 'username';
        $this->homeTeamPrediction = 1;
        $this->awayTeamPrediction = 2;
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
        $this->group->addPlayer($memberId, $this->username, $this->dateOccurred);
        $this->group->clearRecordedEvents();

        $this->playerId = (string) $this->group->getPlayers()[0]->getPlayerId();

        $this->submitScoreForGroupCommand = new SubmitScoreForGroupCommand(
            $this->groupId,
            $this->playerId,
            $this->gameId,
            $this->homeTeamPrediction,
            $this->awayTeamPrediction
        );
    }

    public function testItAddsScoreSubmittedEventToAGroupInTheGroupRepository()
    {
        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();
        $groupRepository->expects($this->once())->method('get')->willReturn($this->group);
        $groupRepository->expects($this->once())->method('add');

        $submitScoreForGroupHandler = new SubmitScoreForGroupHandler(new FakeClock(), $groupRepository);

        $submitScoreForGroupHandler->handle($this->submitScoreForGroupCommand);
    }
}
