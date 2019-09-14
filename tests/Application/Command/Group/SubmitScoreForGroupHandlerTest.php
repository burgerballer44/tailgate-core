<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Application\Command\Group\SubmitScoreForGroupHandler;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\Score;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class SubmitScoreForGroupHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $playerId = '';
    private $userId = 'userId';
    private $gameId = 'gameId';
    private $groupName = 'groupName';
    private $memberId = '';
    private $username = 'username';
    private $homeTeamPrediction = '70';
    private $awayTeamPrediction = '60';
    private $group;
    private $submitScoreForGroupCommand;

    public function setUp()
    {
        // create a group, add a player for the group owner, and clear events
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            $this->groupName,
            UserId::fromString($this->userId)
        );
        $memberId = $this->group->getMembers()[0]->getMemberId();
        $this->memberId = (string) $memberId;
        $this->group->addPlayer($memberId, $this->username);
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
        $groupId = $this->groupId;
        $playerId = $this->playerId;
        $memberId = $this->memberId;
        $gameId = $this->gameId;
        $groupName = $this->groupName;
        $homeTeamPrediction = $this->homeTeamPrediction;
        $awayTeamPrediction = $this->awayTeamPrediction;
        $group = $this->group;

        $groupRepository = $this->getMockBuilder(GroupRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the group
        $groupRepository->expects($this->once())->method('get')->willReturn($group);

        // the add method should be called once
        // the group object should have the ScoreSubmitted event
        $groupRepository->expects($this->once())->method('add')->with($this->callback(
            function ($group) use ($groupId, $playerId, $memberId, $gameId, $homeTeamPrediction, $awayTeamPrediction) {
                $events = $group->getRecordedEvents();

                return $events[0] instanceof ScoreSubmitted
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getScoreId() instanceof ScoreId
                && $events[0]->getPlayerId()->equals(PlayerId::fromString($playerId))
                && $events[0]->getMemberId()->equals(MemberId::fromString($memberId))
                && $events[0]->getGameId()->equals(GameId::fromString($gameId))
                && $events[0]->getHomeTeamPrediction() == $homeTeamPrediction
                && $events[0]->getAwayTeamPrediction() == $awayTeamPrediction
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));
        
        $submitScoreForGroupHandler = new SubmitScoreForGroupHandler($groupRepository);

        $submitScoreForGroupHandler->handle($this->submitScoreForGroupCommand);
    }
}
