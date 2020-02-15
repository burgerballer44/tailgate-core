<?php

namespace Tailgate\Test\Domain\Service\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupScoreUpdated;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Group\UpdateScoreForGroupHandler;

class UpdateScoreForGroupHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $groupInviteCode = 'code';
    private $groupRole = 'groupRole';
    private $homeTeamPrediction = '70';
    private $awayTeamPrediction = '60';
    private $playerId = '';
    private $group;
    private $scoreId;
    private $updateScoreForGroupCommand;

    public function setUp(): void
    {
        // create a group, add a score, and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            $this->groupName,
            $this->groupInviteCode,
            UserId::fromString($this->userId)
        );
        $memberId = $this->group->getMembers()[0]->getMemberId();
        $this->group->addPlayer($memberId, 'username');
        $playerId = $this->group->getPlayers()[0]->getPlayerId();
        $this->group->submitScore($playerId, GameId::fromString('gameId'), 1, 2);
        $this->scoreId = (string) $this->group->getScores()[0]->getScoreId();
        $this->group->clearRecordedEvents();

        $this->updateScoreForGroupCommand = new UpdateScoreForGroupCommand(
            $this->groupId,
            $this->scoreId,
            $this->homeTeamPrediction,
            $this->awayTeamPrediction
        );
    }

    public function testItAddsAGroupScoreUpdatedEventToAGroupInTheGroupRepository()
    {
        $groupId = $this->groupId;
        $scoreId = $this->scoreId;
        $homeTeamPrediction = $this->homeTeamPrediction;
        $awayTeamPrediction = $this->awayTeamPrediction;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the GroupScoreUpdated event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $scoreId, $homeTeamPrediction, $awayTeamPrediction) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof GroupScoreUpdated
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getScoreId()->equals(ScoreId::fromString($scoreId))
                && $events[0]->getHomeTeamPrediction() == $homeTeamPrediction
                && $events[0]->getAwayTeamPrediction() == $awayTeamPrediction
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);
        
        $updateScoreForGroupHandler = new UpdateScoreForGroupHandler($validator, $groupRepository);

        $updateScoreForGroupHandler->handle($this->updateScoreForGroupCommand);
    }
}
