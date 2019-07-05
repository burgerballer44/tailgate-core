<?php

namespace Tailgate\Application\Query\Season;

use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;

class SeasonQueryHandler
{
    private $seasonViewRepository;

    public function __construct(SeasonViewRepositoryInterface $seasonViewRepository)
    {
        $this->seasonViewRepository = $seasonViewRepository;
    }

    public function handle(seasonQuery $seasonQuery)
    {
        return $this->seasonViewRepository->get(SeasonId::fromString($seasonQuery->getSeasonId()));
    }
}