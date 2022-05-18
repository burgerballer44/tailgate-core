<?php

namespace Tailgate\Test\Domain\Service\Season;

use Tailgate\Application\Query\Season\AllSeasonsBySportQuery;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;
use Tailgate\Domain\Service\Season\AllSeasonsBySportQueryHandler;
use Tailgate\Test\BaseTestCase;

class AllSeasonsBySportQueryHandlerTest extends BaseTestCase
{
    public function testItAttemptsToGetAllSeasonsFromSeasonViewRepository()
    {
        $seasonViewRepository = $this->createMock(SeasonViewRepositoryInterface::class);
        $seasonViewTransformer = $this->createMock(SeasonDataTransformerInterface::class);
        $seasonViewRepository->expects($this->once())->method('allBySport')->willReturn([]);

        $allSeasonsQuery = new AllSeasonsBySportQuery(Sport::getFootball());
        $allSeasonsQueryHandler = new AllSeasonsBySportQueryHandler($seasonViewRepository, $seasonViewTransformer);
        $allSeasonsQueryHandler->handle($allSeasonsQuery);
    }
}
