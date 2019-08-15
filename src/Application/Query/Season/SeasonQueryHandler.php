<?php

namespace Tailgate\Application\Query\Season;

use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Application\DataTransformer\SeasonDataTransformerInterface;

class SeasonQueryHandler
{
    private $seasonViewRepository;
    private $seasonViewTransformer;

    public function __construct(
        SeasonViewRepositoryInterface $seasonViewRepository,
        SeasonDataTransformerInterface $seasonViewTransformer
    ) {
        $this->seasonViewRepository = $seasonViewRepository;
        $this->seasonViewTransformer = $seasonViewTransformer;
    }

    public function handle(seasonQuery $seasonQuery)
    {
        $seasonView = $this->seasonViewRepository->get(SeasonId::fromString($seasonQuery->getSeasonId()));
        return $this->seasonViewTransformer->read($seasonView);
    }
}
