<?php

namespace Tailgate\Test\Domain\Service\Season;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Query\Season\AllSeasonsBySportQuery;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;
use Tailgate\Domain\Service\Season\AllSeasonsBySportQueryHandler;

class AllSeasonsBySportQueryHandlerTest extends TestCase
{
    public function testItAttemptsToGetAllSeasonsFromSeasonViewRepository()
    {
        $seasonViewRepository = $this->createMock(SeasonViewRepositoryInterface::class);
        $seasonViewTransformer = $this->createMock(SeasonDataTransformerInterface::class);
        $seasonViewRepository->expects($this->once())->method('allBySport')->willReturn([]);

        $allSeasonsQuery = new AllSeasonsBySportQuery(Season::SPORT_FOOTBALL);
        $allSeasonsQueryHandler = new AllSeasonsBySportQueryHandler($seasonViewRepository, $seasonViewTransformer);
        $allSeasonsQueryHandler->handle($allSeasonsQuery);
    }
}
