<?php

namespace Tailgate\Test\Domain\Service\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Season\SeasonQuery;
use Tailgate\Domain\Service\Season\SeasonQueryHandler;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonView;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;

class SeasonQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetASeasonBySeasonIdFromTeamViewRepository()
    {
        $seasonId = 'seasonId';

        $seasonViewRepository = $this->createMock(SeasonViewRepositoryInterface::class);
        $gameViewRepository = $this->createMock(GameViewRepositoryInterface::class);
        $seasonViewTransformer = $this->createMock(SeasonDataTransformerInterface::class);
        $seasonView = $this->createMock(SeasonView::class);
        $seasonViewRepository->expects($this->once())
            ->method('get')
            ->willReturn($seasonView)
            ->with($this->callback(function ($seasonQuerySeasonId) use ($seasonId) {
                return (new SeasonId($seasonId))->equals($seasonQuerySeasonId);
            }));

        $seasonQuery = new SeasonQuery($seasonId);
        $seasonQueryHandler = new SeasonQueryHandler($seasonViewRepository, $gameViewRepository, $seasonViewTransformer);
        $seasonQueryHandler->handle($seasonQuery);
    }
}
