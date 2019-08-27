<?php

namespace Tailgate\Test\Application\Command\Group;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;
use Tailgate\Application\Command\Group\UpdateScoreForGroupHandler;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupScoreUpdated;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class UpdateScoreForGroupHandlerTest extends TestCase
{
    private $groupId = 'groupId';
    private $userId = 'userId';
    private $groupName = 'groupName';
    private $groupRole = 'groupRole';
    private $homeTeamPrediction = '70';
    private $awayTeamPrediction = '60';
    private $group;
    private $scoreId;
    private $updateScoreForGroupCommand;

    public function setUp()
    {
        $this->group = Group::create(
            GroupId::fromString($this->groupId),
            $this->groupName,
            UserId::fromString($this->userId)
        );
        $gameId = GameId::fromString('gameID');
        $this->group->submitScore(UserId::fromString($this->userId), $gameId, 1, 2);

        $this->scoreId = $this->group->getScores()[0]->getScoreId();
        $this->group->clearRecordedEvents();

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
        $groupRepository
           ->expects($this->once())
           ->method('get')
           ->willReturn($group);

        // the add method should be called once
        // the group object should have the GroupScoreUpdated event
        $groupRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(
                function ($group) use (
                $groupId,
                $scoreId,
                $homeTeamPrediction,
                $awayTeamPrediction
            ) {
                    $events = $group->getRecordedEvents();

                    return $events[0] instanceof GroupScoreUpdated
                && $events[0]->getAggregateId()->equals(GroupId::fromString($groupId))
                && $events[0]->getScoreId() instanceof ScoreId
                && $events[0]->getHomeTeamPrediction() == $homeTeamPrediction
                && $events[0]->getAwayTeamPrediction() == $awayTeamPrediction
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
                }
        ));
        
        $updateScoreForGroupHandler = new UpdateScoreForGroupHandler(
            $groupRepository
        );

        $updateScoreForGroupHandler->handle($this->updateScoreForGroupCommand);
    }
}
